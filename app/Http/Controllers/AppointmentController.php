<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Services\AppointmentService;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $appointmentModel;

    public function __construct(
        AppointmentService $appointmentService,
        Appointment $appointmentModel
    ) {
        $this->appointmentService = $appointmentService;
        $this->appointmentModel = $appointmentModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        return view('appointments', compact('user'));
    }

    public function load(Request $request)
    {
        $appointments = $this->appointmentService->loadAppointments(
            $request->start,
            $request->end
        );

        return response()->json($appointments);
    }

    public function loadDoctor(Request $request)
    {
        $appointments = $this->appointmentService->loadDoctorAppointments(
            $request->id,
            $request->start,
            $request->end
        );

        return response()->json($appointments);
    }

    public function getDoctorsAvailableByDate(Request $request)
    {
        $appointments = $this->appointmentService->getDoctorsAvailableByDate(
            $request->date
        );

        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $storeReponse = $this->appointmentService->store($request->date, $request->doctor);

        if (!$storeReponse->success) {
            return redirect()->back()->withError('Erro ao agendar consulta');
        }

        return redirect()->back()->withSuccess('Consulta agendada!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = auth()->user();

        $query = $this->appointmentModel->where('id', $id);

        if ($user->type !== 'admin') {
            $query = $query->where(function ($query) use ($user) {
                $query->where('patient_id', $user->id)
                    ->orWhere('doctor_id', $user->id);
            });
        }

        $appointment = $query->first();

        if (is_null($appointment)) {
            abort(404);
        }

        return view('appointment', compact('user', 'appointment'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(int $id)
    {
        $cancelReponse = $this->appointmentService->cancel($id);

        if (!$cancelReponse->success) {
            return redirect()->route('appointments.index')->withError('Erro ao cancelar consulta');
        }

        return redirect()->route('appointments.index')->withSuccess('Consulta cancelada!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $destroyReponse = $this->appointmentService->destroy($id);

        if (!$destroyReponse->success) {
            return redirect()->route('appointments.index')->withError('Erro ao deletar consulta');
        }

        return redirect()->route('appointments.index')->withSuccess('Consulta deletada!');
    }
}
