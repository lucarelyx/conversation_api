<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Conversation;

class ConversationController extends Controller
{
    public function processRequest(Request $request)
    {
        $user_id = $request->input('user_id');
        $query = $request->input('query');

        // Verificar si existe una conversación para el user_id dado
        $conversation = Conversation::where('user_id', $user_id)->first();

        // Crear un array para los mensajes en el formato esperado por OpenAI
        $messages = [];
        if($conversation){

            $previousMessages = Conversation::where('user_id', $user_id)
            ->orderBy('created_at', 'asc')
            ->get();
            foreach ($previousMessages as $message) {
                $messages[] = ["role" => $message->role, "content" => $message->content];
            }
            
        }

        if (!$conversation) {
            // Si no se encuentra una conversación, crear una nueva
            $previousMessages = null;
        }
        

        
        

        // Agregar el nuevo mensaje a la conversación
        $messages[] = ["role" => "user", "content" => $query];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer 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',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'temperature' => 0.7,
        ]);

        $completion = $response->json();

        if (isset($completion['choices'][0]['message']['content'])) {
            $content = $completion['choices'][0]['message']['content'];

            // Guardar el nuevo mensaje y la respuesta en la base de datos
            $userMessage = new Conversation();
            $userMessage->user_id = $user_id;
            $userMessage->role = 'user';
            $userMessage->content = $query;
            $userMessage->save();

            $aiMessage = new Conversation();
            $aiMessage->user_id = $user_id;
            $aiMessage->role = 'assistant';
            $aiMessage->content = $content;
            $aiMessage->save();

            return response()->json(['completion' => $content]);
        } else {
            return response()->json(['error' => 'No se pudo obtener la respuesta de OpenAI'], 500);
        }
    }



    /*
    public function store(Request $request){
        $response = Http::withHeaders([
            'Authorization' => 'Bearer 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',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ["role" => "user", "content" => $request->input('query')],
            ],
            'temperature' => 0.7,
        ]);

        $completion = $response->json();

        if (isset($completion['choices'][0]['message']['content'])) {
            $content = $completion['choices'][0]['message']['content'];
            return response()->json(['completion' => $content]);
        } else {
            return $response->json();
        }
    }*/
}
