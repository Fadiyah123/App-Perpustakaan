<?php

namespace App\Http\Controllers;
use App\Models\Book;
use App\Models\Borrows;
use App\Models\DetailBorrows;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $totalBooks = Book::count();
        $totalStock = Book::sum('stock');

        //detail_buku ada/tidak ada yang sedang dipinjam, actual_date = null;
        //select * from detail_borrows join book on book.id = detail_borrows.id_book
        // join borrows on borrows.id = detail_borrows.id_borrow WHERE actual_date = null
        $borrowedBooks = DetailBorrows::with('book', 'borrow')->whereHas('borrow', function($q){
            $q->whereNull('actual_return_date');
        })->count();

        $returnBooks = Borrows::where('status', 0)->whereNotNull('actual_return_date')->count();
        $notReturnBooks = Borrows::where('status', 1)->whereNotNull('actual_return_date')->count();

        $fines = Borrows::with('member')->where('fine', '>', 0)->get();
        $totalfines = Borrows::sum('fine');
        return view('admin.dashboard', compact('totalBooks', 'totalStock', 'borrowedBooks', 'returnBooks', 'notReturnBooks', 'fines', 'totalFines'));
    }
}