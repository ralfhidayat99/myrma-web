<?php

namespace App\Http\Controllers;

use App\Http\Controllers\api\notificationController;
use App\Jobs\TglFormatter;
use App\Models\Atasan;
use App\Models\FcmToken;
use App\Models\Lembur;
use App\Models\User;
use Illuminate\Http\Request;

class LemburController extends Controller
{
    public function index(Request $request)
    {
        $tglFormat = new TglFormatter();
        $perPage = $request->input('per_page', 5);
        $data['menu'] = 'dashboard';
        $lemburan = Lembur::where('id_user', auth()->user()->id)->orderBy('id', 'desc')->paginate($perPage);
        foreach ($lemburan as $key => $value) {
            $lemburan[$key]->tanggal = $tglFormat->tgl_format($value->tanggal);
        }
        $data['lemburan'] = $lemburan;
        $links = $lemburan->links();

        return view('pages/home', $data);
    }

    public function range($tgl, Request $request)
    {

        $tglRamge = explode(' to ', $tgl);

        $tglFormat = new TglFormatter();
        $perPage = $request->input('per_page', 5);


        $data['menu'] = 'dashboard';
        $lemburan = Lembur::where('id_user', auth()->user()->id)
            ->whereBetween('tanggal', [$tglRamge[0], $tglRamge[1]])->orderBy('id', 'desc')->paginate($perPage);
        foreach ($lemburan as $key => $value) {
            $lemburan[$key]->tanggal = $tglFormat->tgl_format($value->tanggal);
        }
        $data['lemburan'] = $lemburan;

        // $user = auth()->user()->id;
        // return response($lemburan);

        return view('pages/home', $data);
    }
    public function lembur()
    {
        $data = [
            'menu' => 'lembur',
            'is_office' => session('divisi') == 'produksi' ? false : true
        ];

        return view('pages/lembur/formlembur', $data);
    }
    public function lemburForOther()
    {
        $data = [
            'menu' => 'lembur',
            'employee' => User::where('id_atasan', session('id'))->get()
        ];
        // dd(session()->user);

        return view('pages/lembur/formlemburother', $data);
    }

    public function store(Request $request)
    {
        if (session('divisi') != 'produksi') {
            $validateData = $request->validate([
                'tanggal' => 'required',
                'alasan' => 'required',
                'is_hari_libur' => 'required',
            ]);
            $validateData['jam_mulai'] = '17:00';
        } else {
            $validateData = $request->validate([
                'tanggal' => 'required',
                'alasan' => 'required',
                'is_hari_libur' => 'required',
                'jam_mulai' => 'required'
            ]);
        }


        $user = auth()->user();

        $validateData['id_user'] = $user->id;
        // return response($request);

        $lembur = Lembur::create($validateData);
        $data = [
            'nama' => 'lembur ' . $user->name,
            'pesan' => $validateData['alasan']
        ];
        // dd($data);
        if ($lembur) {
            return $this->sendFCM($data);
        }
    }
    public function storeOther(Request $request)
    {
        // dd($request->all());
        $validateData = $request->validate([
            'tanggal' => 'required',
            'alasan' => 'required',
            'is_hari_libur' => 'required',
            'untuk' => 'required',
        ]);

        foreach ($validateData['untuk'] as $value) {

            $validateData['id_user'] = $value;
            $lembur = Lembur::create($validateData);
        }
        // dd($validateData);
        $user = auth()->user();
        // return response($request);

        $data = [
            'nama' => $user->name,
            'pesan' => 'mengajukan lembur untuk beberapa orang'
        ];
        // dd($data);
        if ($lembur) {
            return $this->sendFCM($data);
        }
    }

    public function cancel($id)
    {
        $lembur = Lembur::find($id);
        $lembur->approve = 3;
        $lembur->save();
        return back();
    }

    public function successAlert()
    {
        $data['menu'] = 'Form Lembur';
        // dd($data);
        return view('pages/success', $data);
    }

    function sendFCM($data)
    {
        // API access key dari Firebase Console
        // define('API_ACCESS_KEY', 'YOUR_API_ACCESS_KEY');
        $tokens = FcmToken::all();
        $registrationIds = [];
        foreach ($tokens as $value) {
            array_push($registrationIds, $value->token);
        }
        // dd($registrationIds);
        $serverKey = 'AAAAY2qDDgY:APA91bEdlyP6LLhBp8rNaaMAS-i1neMVlHQc0oNsgKq5HmNjteOWeHv_fzqWcXAj7TvO4CPIp2CqZQqXo1AMSLJLAa7wXUDCBbQJPk5A0oYUr2JQ7SLwBTRaBOUB3tlRQh2h-Fb3eF65';
        // $registrationIds = array('ce9FAt9DRc2yDpGXJpZKJ9:APA91bHOTUrIrZsun2hwz_XJubUC4woBfyUC_-CTQFhPCn6TmOMtVPwO5ocCkjJSSqFriBdU8B0eDb-pPLU1TZUvCU42yDs7mzFcc0RlzkyMH011TkY8BIxS4ct5JmF9tuM6G5zzFiPb', 'fcZ8_1YlRq6wOcELi6SYct:APA91bGO1G9CKyNOiV0jbi-qmS6PtNHkVf39EftrCdiy-ogZPEP0__BGDIY1zvLLeyPyE9rKdSTLCoLMOhiZvM_VMdoR62XbBSSz12i3oaTxBKG19A67PMN1-AmBTn9KXLwwm5u5gho6');

        // Data payload yang ingin Anda kirim
        $data = array(
            'menu' =>  $data['nama'],
            'body' => $data['pesan'],
            // tambahkan data payload lainnya sesuai kebutuhan
        );

        $fields = array(
            'registration_ids' => $registrationIds,
            'data' => $data
        );

        $headers = array(
            'Authorization: key=' . $serverKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        // ob_start();
        $result = curl_exec($ch);
        // ob_get_clean();
        curl_close($ch);

        $data['menu'] = 'Form Lembur';
        return redirect()->route('status');
    }
}
