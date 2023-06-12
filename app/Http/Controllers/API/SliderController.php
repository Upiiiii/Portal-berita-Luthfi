<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;

class SliderController extends Controller
{
    public function index()
    {
        try {
            $slider = Slider::latest()->paginate('10');

            if ($slider) {
                return ResponseFormatter::success($slider, 'Data slider berhasil diambil');
            }  else {
                return ResponseFormatter::error($slider, 'Data slider gagal diambil', 404);
            };

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
                'image' => 'required|mimes:png,jpg,jpeg',
                'url' => 'required'
            ]);

            $image = $request->file('image');
            $image->storeAs('public/sliders', $image->hashName());

            $slider = Slider::create([
                'image' => $image->hashName(),
                'url' => $request->url()
            ]);

            if ($slider) {
                return ResponseFormatter::success($slider, 'Data slider berhasil ditambahkan');
            } else {
                return ResponseFormatter::error(null, 'Data slider gagal ditambahkan', 404);
            }

        } catch (\Error $error) {
            return ResponseFormatter::error([
                'data' => null,
                'message' => 'Data Gagal ditambahkan',
                'error' => $error
            ]);

        }
    }
    
}