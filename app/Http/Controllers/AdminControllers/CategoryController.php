<?php

namespace App\Http\Controllers\AdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();

        return inertia('Admin/Category/Index', [
            'categories' => $categories
        ]);
    }

    public function create()
    {
        return inertia('Admin/Category/Create');
    }

    public function store(Request $request)
    {
        $validate = $request->validate([
            'name' => 'required|string|min:5|unique:categories,name',
            'description' => 'nullable|string|max: 300'
        ]);

        $name = Category::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->exists();

        if ($name) {
            return redirect()->back()->withErrors(['name' => 'Category already exist'])->withInput();
        } else {
            Category::create($validate);
            return redirect()->route('admin.categories.index')->with('message', 'Category created successfully');
        }
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return inertia('Admin/Category/Edit', [
            'category' => $category
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validate = $request->validate([
            'name' => 'required|string|min:5|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:300'
        ]);

        $name = Category::whereRaw('LOWER(name) = ?', [strtolower($request->name)])->exists();

        if ($name && strtolower($request->name) != strtolower($category->name)) {
            return redirect()->back()->withErrors(['name' => 'Category already exist'])->withInput();
        } else {
            $category->update($validate);
            return redirect()->route('admin.categories.index')->with('message', 'Category updated successfully');
        }
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);

        return inertia('Admin/Category/View', [
            'category' => $category
        ]);
    }

    public function book($id){
        $category = Category::with('books')->findOrFail($id);
        return inertia('Admin/Category/Books', [
            'category' => $category,
            'books' => $category->book
        ]);
    }
    
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return redirect()->route('admin.categories.index')->with('message', 'Category deleted successfully');
    }
}
