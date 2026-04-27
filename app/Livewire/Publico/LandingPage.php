<?php

namespace App\Livewire\Publico;

use App\Models\Producto;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactoMail;

#[Layout('layouts.guest')] 
class LandingPage extends Component
{
    public $nombre = '';
    public $email = '';
    public $mensaje = '';

    public function enviarMensaje()
    {
        $this->validate([
            'nombre'  => 'required|string|max:100',
            'email'   => 'required|email|max:100',
            'mensaje' => 'required|string|min:10|max:1000',
        ]);

        try {
            Mail::to('admin@fundacovi.org')->send(new ContactoMail([
                'nombre'  => $this->nombre,
                'email'   => $this->email,
                'mensaje' => $this->mensaje,
            ]));

            $this->reset(['nombre', 'email', 'mensaje']);
            $this->dispatch('notify', message: '¡Mensaje enviado con éxito! Te contactaremos pronto.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Hubo un error al enviar el correo. Intenta más tarde.', type: 'error');
        }
    }

    public function render()
    {
        $productos = Producto::with('apiario')->where('estado_activo', true)->get();

        $apicultores = User::whereHas('role', fn($q) => $q->where('nombre_rol', 'Apicultor'))
            ->where('estado_activo', true)
            ->get();

        return view('livewire.publico.landing-page', [
            'productos'   => $productos,
            'apicultores' => $apicultores,
        ]);
    }
}
