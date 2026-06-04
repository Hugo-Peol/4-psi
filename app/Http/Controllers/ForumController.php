<?php

namespace App\Http\Controllers;

use App\Models\ForumPost;
use App\Models\ForumReply;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $query = ForumPost::with('user')->latest();

        if ($request->filled('category')) {
            $query->byCategory($request->category);
        }

        $posts = $query->paginate(15);

        $categories = [
            'casos_clinicos' => 'Casos Clínicos',
            'materiais'      => 'Materiais',
            'indicacoes'     => 'Indicações',
            'duvidas'        => 'Dúvidas',
            'geral'          => 'Geral',
        ];

        return view('forum.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = [
            'casos_clinicos' => 'Casos Clínicos',
            'materiais'      => 'Materiais',
            'indicacoes'     => 'Indicações',
            'duvidas'        => 'Dúvidas',
            'geral'          => 'Geral',
        ];

        return view('forum.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => 'required|string|max:255',
            'body'      => 'required|string|min:20',
            'category'  => 'required|in:casos_clinicos,materiais,indicacoes,duvidas,geral',
            'anonymous' => 'boolean',
        ]);

        $data['user_id']   = auth()->id();
        $data['anonymous'] = $request->boolean('anonymous', true);

        ForumPost::create($data);

        return redirect()->route('forum.index')
            ->with('success', 'Post publicado com sucesso!');
    }

    public function show(ForumPost $forum)
    {
        $forum->incrementViews();
        $forum->load('replies.user', 'user');
        $replies = $forum->replies()->with('user')->paginate(20);

        return view('forum.show', compact('forum', 'replies'));
    }

    public function storeReply(Request $request, ForumPost $forum)
    {
        $data = $request->validate([
            'body'      => 'required|string|min:5',
            'anonymous' => 'boolean',
        ]);

        $data['user_id']      = auth()->id();
        $data['forum_post_id']= $forum->id;
        $data['anonymous']    = $request->boolean('anonymous', true);

        ForumReply::create($data);

        return back()->with('success', 'Resposta publicada!');
    }

    public function destroy(ForumPost $forum)
    {
        abort_if($forum->user_id !== auth()->id(), 403);
        $forum->delete();

        return redirect()->route('forum.index')
            ->with('success', 'Post excluído.');
    }
}
