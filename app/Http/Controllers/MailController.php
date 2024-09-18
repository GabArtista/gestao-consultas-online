<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailController extends Controller
{
    public function sendEmail(Request $request)
    {
        $request->validate([
            'nome' => 'required|string',
            'email' => 'required|email',
            'telefone' => 'required|string',
            'mensagem' => 'required|string',
        ]);

        $mail = new PHPMailer(true);

        try {
            // Configurações do servidor
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable verbose debug output
            $mail->isSMTP(); // Send using SMTP
            $mail->Host = 'smtp.gmail.com'; // Set the SMTP server to send through
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = 'c1a9x9a9@gmail.com'; // SMTP username
            $mail->Password = 'bqho bfzh tamj qjin'; // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Enable implicit TLS encryption
            $mail->Port = 465; // TCP port to connect to

            // Destinatários
            $mail->setFrom('c1a9x9a9@gmail.com', 'Mailer');
            $mail->addAddress('wfimoveis10@gmail.com', 'Joe User'); // Add a recipient
            $mail->addReplyTo('c1a9x9a9@gmail.com', 'Information');

            // Conteúdo do e-mail
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Chegou um novo Lead da Landing Page de Captação';

            $body = "Lead da Landing Page, segue informação de contato:<br>
            Nome: {$request->nome}<br>
            E-mail: {$request->email}<br>
            Telefone: {$request->telefone}<br>
            Mensagem: {$request->mensagem}<br>";

            $mail->Body = $body;
            $mail->AltBody = "Novo lead da Landing Page:\nNome: {$request->nome}\nE-mail: {$request->email}\nTelefone: {$request->telefone}\nMensagem: {$request->mensagem}";

            $mail->send();

            // Redirecionar para o link do grupo de WhatsApp
            return redirect('https://chat.whatsapp.com/CuztnkSsLZm3Z9qE01hML5')->with('success', 'E-mail enviado com sucesso!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', "Erro no envio do e-mail: {$mail->ErrorInfo}");
        }
    }
}
