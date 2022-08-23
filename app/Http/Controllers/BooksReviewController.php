<?php

namespace App\Http\Controllers;

use App\{ BookReview, Book };
use App\Http\Requests\PostBookReviewRequest;
use App\Http\Resources\BookReviewResource;
use Illuminate\Http\Request;
use DB;

class BooksReviewController extends Controller
{
    public function __construct()
    {

    }

    public function store(int $bookId, PostBookReviewRequest $request)
    {
        DB::beginTransaction();
        try {
            $book = Book::find($bookId);
            if(!$book) {
                return response()->json([
                    'message' => 'Book not found'
                ], 404);
            }

            $bookReview = new BookReview();

            $bookReview->book_id = $bookId;
            $bookReview->user_id = auth()->user()->id;
            $bookReview->review = $request->review;
            $bookReview->comment = $request->comment;
            $bookReview->save();

            DB::commit();
            return response()->json(['data' => new BookReviewResource($bookReview)], 201);
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $bookId, int $reviewId, Request $request)
    {
        DB::beginTransaction();
        try {
            $bookReview = BookReview::find($reviewId);
            if(!$bookReview) {
                return response()->json([
                    'message' => 'Book review not found'
                ], 404);
            }

            $book = Book::find($bookId);
            if(!$book) {
                return response()->json([
                    'message' => 'Book not found'
                ], 404);
            }

            $bookReview->delete();

            DB::commit();
            return response()->noContent();
        } catch (\Throwable $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
