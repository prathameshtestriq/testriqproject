<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function clear_search()
    {
        session::forget('category_name');
        return redirect('/category');
    }

    public function index_category(Request $request)
    {
        $aReturn = array();
        $aReturn['search_category'] = '';

        if (isset($request->form_type) && $request->form_type == 'search_category') {
            session(['category_name' => $request->category_name]);
            return redirect('/category');
        }
        $aReturn['search_category'] = (!empty(session('category_name'))) ? session('category_name') : '';

        $CountRows = Category::get_count($aReturn);
        $PageNo = request()->input('page', 1);
        $Limit = config('custom.per_page');
        // $Limit = 3;
        $aReturn['Offset'] = ($PageNo - 1) * $Limit;

       
        $aReturn["category_array"] = Category::get_all_category($Limit,$aReturn);
       
        $aReturn['Paginator'] = new LengthAwarePaginator($aReturn['category_array'], $CountRows, $Limit, $PageNo);
        $aReturn['Paginator']->setPath(request()->url());

        return view('category.list', $aReturn);
    }



    public function add_edit_category(Request $request, $iId = 0)
    {
        $aReturn = [
            'id' => '',
            'category_name' => '',
            'category_logo' => '',
            'status' => 1,
            'events' => Event::all()->pluck('name', 'id')->toArray(),
        ];

        if ($request->has('form_type') && $request->form_type == 'add_edit_category') {
            $rules = [
                'category_name' => 'required',
                'category_logo' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }

            $events = $request->event_id ?? [];

            if ($iId > 0) {
                // Update the category
                $result = Category::update_category($iId, $request->all());
                $successMessage = 'Category updated successfully';
                $CategoryId = $iId;

                // Delete existing records for this category
                $deleteQuery = 'DELETE FROM event_category WHERE category_id = :category_id';
                DB::delete($deleteQuery, ['category_id' => $CategoryId]);

                // Insert new records
                foreach ($events as $event) {
                    $sql = "INSERT INTO event_category (event_id, category_id, created_by) VALUES(:event_id, :category_id, :created_by)";
                    $Bind = [
                        ":event_id" => $event,
                        ":category_id" => $CategoryId,
                        ":created_by" => 1
                    ];
                    DB::insert($sql, $Bind);
                }
            }
             else {
                $result = Category::add_category($request->all());
                $CategoryId = DB::getPdo()->lastInsertId();

                if (!empty($events) && !empty($CategoryId)) {
                    foreach ($events as $event) {
                        $sql = "INSERT INTO event_category (event_id, category_id, created_by) VALUES(:event_id, :category_id, :created_by)";
                        $Bind = [
                            ":event_id" => $event,
                            ":category_id" => $CategoryId,
                            ":created_by" => 1
                        ];
                        DB::insert($sql, $Bind);
                    }
                }

                $successMessage = 'Category added successfully';
            }

            return redirect('/category')->with('success', $successMessage);
        } else {
            if ($iId > 0) {
                $category = Category::find($iId);

                if ($category) {
                    $aReturn = [
                        'id' => $category->id,
                        'category_name' => $category->name,
                        'category_logo' => $category->logo,
                        'status' => $category->active,
                        'events' => Event::all()->pluck('name', 'id')->toArray(),
                    ];
                }
            }

            foreach ($aReturn['events'] as $eventId => $eventData) {
                $cat_Id = $aReturn['id'];
                $result = DB::select("SELECT * FROM event_category WHERE event_id = ? AND Category_id = ?", [$eventId, $cat_Id]);
                $aReturn['result'][$eventId] = $result;

                $isSelected = sizeof($result) > 0 ? true : false;

                $aReturn['events'][$eventId] = [
                    'name' => $eventData,
                    'selected' => $isSelected,
                ];
            }

            if ($iId > 0 && isset($aReturn['event_id'])) {
                $aReturn['event'] = Event::find($aReturn['event_id']);
            }

            return view('category.create', ['aReturn' => $aReturn]);
        }
    }




    public function change_active_status_category(Request $request)
    {
        $aReturn = Category::change_active_status_category($request);
        return $aReturn;
    }

    public function delete_category($iId)
    {
        Category::delete_category($iId);
        return redirect(url('/category'))->with('success', 'Category deleted successfully');
    }

    // public function uploadFile($file, $path)
    // {
    //     $extension = $file->getClientOriginalExtension();
    //     $imageName = time() . '.' . $extension;
    //     $file->move($path, $imageName);
    //     return $imageName;
    // }



}
