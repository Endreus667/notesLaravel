<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Models\User;
use App\Services\Operations;
use Illuminate\Http\Request;

class MainController extends Controller
{
    // Exibir as notas do usuário
    public function index() {
        // Obter o ID do usuário da sessão
        $id = session('user.id');

        // Carregar as notas do usuário
        $notes = User::find($id)
        ->notes()
        ->wherenull('deleted_at')
        ->get()
        ->toArray();

        // Exibir a view com as notas
        return view('home', ['notes' => $notes]);
    }

    // Exibir a página para criar uma nova nota
    public function newNote() {
        return view('new_note');
    }

    // Submeter a criação de uma nova nota
    public function newNoteSubmit(Request $request) {
        // Validar a entrada
        $request->validate([
            'text_title' => 'required|string|min:3|max:16',
            'text_note' => 'required|string|min:6|max:3000',
        ], [
            'text_title.required' => 'O título é obrigatório.',
            'text_title.string' => 'O título deve ser uma string.',
            'text_title.min' => 'O título deve ter no mínimo 3 caracteres.',
            'text_title.max' => 'O título deve ter no máximo 16 caracteres.',
            'text_note.required' => 'A nota é obrigatória.',
            'text_note.string' => 'A nota deve ser uma string.',
            'text_note.min' => 'A nota deve ter no mínimo 6 caracteres.',
            'text_note.max' => 'A nota deve ter no máximo 3000 caracteres.',
        ]);

        // Obter o ID do usuário da sessão
        $id = session('user.id');

        // Criar a nova nota
        $note = new Note();
        $note->user_id = $id;
        $note->title = $request->text_title;
        $note->text = $request->text_note;
        $note->save();

        // Redirecionar para a página inicial após salvar a nota
        return redirect()->route('home');
    }

    // Exibir a página para editar uma nota existente
    public function editNote($id) {
        // Descriptografar o ID da nota
        $id = Operations::decryptId($id);

        // Carregar a nota para edição
        $note = Note::find($id);

        // Verificar se a nota existe
        if (!$note) {
            return redirect()->route('home')->with('error', 'Nota não encontrada!');
        }

        // Exibir a view de edição com a nota carregada
        return view('edit_note', ['note' => $note]);
    }

    // Submeter a edição de uma nota
    public function editNoteSubmit(Request $request, $id) {
        // Validar a entrada
        $request->validate([
            'text_title' => 'required|string|min:3|max:16',
            'text_note' => 'required|string|min:6|max:3000',
        ], [
            'text_title.required' => 'O título é obrigatório.',
            'text_title.string' => 'O título deve ser uma string.',
            'text_title.min' => 'O título deve ter no mínimo 3 caracteres.',
            'text_title.max' => 'O título deve ter no máximo 16 caracteres.',
            'text_note.required' => 'A nota é obrigatória.',
            'text_note.string' => 'A nota deve ser uma string.',
            'text_note.min' => 'A nota deve ter no mínimo 6 caracteres.',
            'text_note.max' => 'A nota deve ter no máximo 3000 caracteres.',
        ]);

        // Carregar a nota com base no ID
        $note = Note::find($id);

        // Verificar se a nota existe
        if (!$note) {
            return redirect()->route('home')->with('error', 'Nota não encontrada!');
        }

        // Atualizar os dados da nota
        $note->title = $request->text_title;
        $note->text = $request->text_note;

        // Salvar a nota no banco de dados
        $note->save();

        // Redirecionar para a página inicial após a edição
        return redirect()->route('home')->with('success', 'Nota editada com sucesso!');
    }




    // Excluir uma nota
    public function deleteNote($id) {
        // Descriptografar o ID da nota
        $id = Operations::decryptId($id);

        // lodad note
        $note = Note::find($id);

        //show dele not confirmation
        return view('delete-note', ['note' => $note]);

        //
        return redirect()->route('home')->with('error', 'Nota não encontrada!');
    }

    public function deleteNoteConfirm($id) {
        //check if $id is encrypted
        $id = Operations::decryptId($id);
        //load note
        $note = Note::find($id);
        //1. hard delete
        //$note->delete();
        //2. soft to home
        $note->deleted_at = date('Y-m-d H:i:s');
        $note->save();
        // redirect to home
        return redirect()->route('home');
    }
}
