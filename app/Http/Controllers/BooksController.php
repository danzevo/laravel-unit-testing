<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\PostBookRequest;
use App\Http\Resources\BookResource;
use Illuminate\Http\Request;
use DB;

class BooksController extends Controller
{
    public function __construct()
    {

    }

    public function index(Request $request)
    {
        try {
            $page = $request->filled('page') ? $request->page : 1;

            $sortColumn = $request->filled('sortColumn') ? $request->sortColumn : false;
            $sortDirection = $request->filled('sortDirection') ? $request->sortDirection : 'ASC';

            $title = $request->filled('title') ? $request->title : false;
            $authors = $request->filled('authors') ? $request->authors : false;

            $query = Book::with('reviews', 'authors');

            if($title) {
                $query->where('title', 'like', '%'.$title.'%');
            }

            if($authors) {
                $query->whereHas('authors', function($q) use ($authors) {
                    $idsArr = explode(',', $authors);
                    $q->whereIn('id', $idsArr);
                });
            }

            if($sortColumn == 'avg_review') {
                $query->withCount(['reviews as average_review' => function($q) {
                    $q->select(DB::raw('coalesce(avg(review), 0)'));
                }])->orderBy('average_review', $sortDirection);
            } elseif($sortColumn == 'title' || $sortColumn == 'published_year') {
                $query->orderBy($sortColumn, $sortDirection);
            }

            return BookResource::collection($query->paginate(15, ['*'], 'page', $page));
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(PostBookRequest $request)
    {
        DB::beginTransaction();
        try {
            // @TODO implement
            $book = new Book();

            $book->isbn = $request->isbn;
            $book->title = $request->title;
            $book->description = $request->description;
            $book->published_year = $request->published_year;
            $book->save();
            $book->authors()->sync($request->authors);

            DB::commit();
            return response()->json(['data' => new BookResource($book)], 201);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
