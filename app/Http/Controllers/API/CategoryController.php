<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Category;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Catch_;
use Symfony\Component\HttpFoundation\Test\Constraint\ResponseFormatSame;

class CategoryController extends Controller
{
    public function index()
    {
        try {
            $category = Category::latest()->paginate('10');

            if ($category) {
                return ResponseFormatter::success($category, 'Data category berhasil diambil');
            }
        } catch (\Error $error) {
            return ResponseFormatter::error([
                'message' => 'Something went wrong',
                'error' => $error
            ], 'Authentication Failed', 500);
        }
    }

    public function create(Request $request)
    {
        try {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'image' => 'required|mimes:png,jpg,jpeg'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/categories', $image->hashName());

            $category = Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
                'image' => $image->hashName()
            ]);

            if ($category) {
                return ResponseFormatter::success($category, 'Data category berhasil ditambahkan');
            } else {
                return ResponseFormatter::error(null, 'Data category gagal ditambahkan', 404);
            }
        } catch (\Error $error) {
            return ResponseFormatter::error([
                'data' => null,
                'message' => 'Data Gagal ditambahkan',
                'error' => $error
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            Storage::disk('local')->delete('public/categories/'. basename($category->image));

            $category->delete();

            if ($category) {
                return ResponseFormatter::success($category, 'Data category berhasil dihapus');
            } else {
                return ResponseFormatter::error(null, 'Data category tidak ada', 404);
            }
        } catch (\Error $error) {
            return ResponseFormatter::error([
                'data' => null,
                'message' => 'Data gagal dihapus',
                'error' => $error
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // Request validate
            $this->validate($request, [
                'name'  =>  'required|unique:categories, name,' . $id,
                'imgae' =>  'mimes:png,jpg,jpeg|max:2000'
            ]);

            // get data category by id
            $category   =   Category::findOrFail($id);

            // Check jika imgae kosong
            if ($request->file('image') == '') {
                
                // Update tanpa image
                $category->update([
                    'name'  =>  $request->name,
                    'slug'  =>  Str::slug($request->name, '-')
                ]);

                if ($category) {
                    return ResponseFormatter::success($category, 'Data category berhasil diupdate');
                } else {
                    return ResponseFormatter::error(null, 'Data category tidak ada', 404);
                }
            } else {

                Storage::disk('local')->delete('public/categories/' . basename($category->image));

                $image = $request->file('image');
                $image->storeAs('public/categories', $image->hashName());

                $category = Category::findOrFail($category->id);
                $category->update([
                'image' => $image->hashName(),
                'name' => $request->name,
                'slug' => Str::slug($request->name, '-'),
            ]);

            if ($category) {
                return ResponseFormatter::success($category, 'Data category berhasil dihapus');
            } else {
                return ResponseFormatter::error(null, 'Data category tidak ada', 404);
            }

            }

            


        } catch (\Error $error) {
            return ResponseFormatter::error([
                'data'  =>  null,
                'message'   =>  'Data gagal diupdate',
                'error' =>  $error
            ]);
        }
    }

    public function show($id)
    {
      try {
        $category = Category::findOrFail($id);

        if ($category) {
            return ResponseFormatter::success($category, 'Data berhasil ditampilkan');
        } else {
            return ResponseFormatter::error(null, 'Data berhasil ditampilkan', 400);
        }
            
      } catch (\Error $error) {
        return ResponseFormatter::error([
            'data' => null,
            'message' => 'Data gagal dilihat',
            'error' => $error
        ]);
      }

    }

    
}
    
            
                
            
            
        
