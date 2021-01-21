<?php

namespace App\Http\Controllers;

use App\Article;


use App\Http\Resources\ArticleResource;

use Illuminate\Http\Request;

use Response;

use Illuminate\Support\Facades\Crypt;



class ArticleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::paginate(5);

        return ArticleResource::collection($articles);
    }
	
    public function store(Request $request)
    {
       $article = $request->isMethod('put') ? Article::findOrFail($request->article_id) : new Article;
       
       $article->id = $request->input('article_id');
       $article->title = $request->input('title');
       $article->article = $request->input('article');

       if($article->save()){
           return new ArticleResource($article);
       }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $article  = Article::findOrFail($id);

        return new ArticleResource($article);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $article  = Article::findOrFail($id);

        if($article->delete()){
            return new ArticleResource($article);
        }

     
    }
}