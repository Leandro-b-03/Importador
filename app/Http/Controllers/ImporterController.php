<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Excel;
use Schema;
use Taxonomy;
use App\Data;
use App\File;
use App\Term;
use App\Folder;
use App\ColumnNew;
use App\Vocabulary;
use App\CustomField;
use App\Http\Requests;
use App\DataCustomField;

class ImporterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Get all the files uploads and they status
        $files = File::orderBy('created_at', 'desc')->paginate(15);

        return view('welcome')->with('files', $files);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $inputs = $request->all();

        try {
            if($inputs) {
                ini_set('memory_limit','5512M');
                ini_set('MAX_EXECUTION_TIME', -1);
                set_time_limit(0);

                $name = $inputs['file']->getClientOriginalName();

                // die(d($inputs['file']->getClientOriginalName()));

                $this->file = File::create( array('name' => $name ,false) );
                $this->columns = false;

                if ($this->file) {
                    Excel::load($inputs['file']->getRealPath(), function($reader) {
                        // Getting all results
                        $results = $reader->get();

                        foreach ($results as $sheet) {
                            $folder = Folder::create( array('file_id' => $this->file->id, 'name' => $sheet->getTitle()) );

                            if ($sheet->getTitle() != 'IDENTIFICAÇÃO') {
                                $grupo = '';
                                foreach ($sheet as $row) {
                                    if (!is_float($row->id)) {
                                        if ($grupo != $row->id)
                                            $grupo = $row->id;
                                    } else {
                                        $_row = $row->toArray();
                                        $_row['folder_id'] = $folder->id;
                                        $_row['posicao'] = intval($row->id);
                                        $_row['grupo'] = $grupo;

                                        $columns = array();
                                        $custom_fields = array();

                                        foreach ($_row as $key => $value) {
                                            $vocabulary = Vocabulary::where('name', $key)->get()->first();
                                            if (!$vocabulary) {
                                                $term = Term::where('name', $key)->get()->first();

                                                if ($term) {
                                                    $_row[$term->vocabulary()->getResults()->name] = $value;
                                                } else {
                                                    $custom_field = CustomField::where('name', $key)->get()->first();
                                                    
                                                    if ($custom_field) {
                                                        $custom_fields[] = array(
                                                            'custom_field_id' => $custom_field->id,
                                                            'value' => (is_object($value) ? $value->toDateTimeString() :  $value)
                                                        );
                                                    } else {
                                                        $columns[] = array(
                                                            'folder_id' => $folder->id,
                                                            'posicao' => intval($row->id),
                                                            'column' => $key,
                                                            'value' => (is_object($value) ? $value->toDateTimeString() :  $value)
                                                        );

                                                        $this->columns = true;
                                                    }
                                                }
                                            }
                                        }

                                        $_row = Data::create( $_row );

                                        $total_columns = count($columns);

                                        if ($total_columns > 0) {
                                            foreach ($columns as &$column) {
                                                $column['data_id'] = $_row->id;
                                                $column = ColumnNew::create( $column );
                                            }
                                        }

                                        $total_custom_field = count($custom_fields);

                                        foreach ($custom_fields as &$custom_field) {
                                            $custom_field['data_id'] = $_row->id;
                                            $custom_field = DataCustomField::create( $custom_field );
                                        }
                                    }
                                }
                            }
                        }
                    });
                } else {
                    Log::error('Error trying to save the file');
                    return redirect('/');
                }

                $this->file->full_read = true;
                if ($this->file->save()) {
                    if ($this->columns)
                        return redirect('columns');
                    else
                        return redirect('/');
                } else {
                    Log::error('Error trying to save the file');
                    return redirect('/');
                }
            }
        } catch (Exception $e) {
            Log::error($e);
            return redirect('/');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function columns(Request $request)
    {
        // Get all the files uploads and they status
        $columns = ColumnNew::groupBy('column')->get();
        $fields = Schema::getColumnListing('datas');
        
        array_unshift($fields, 'Novo Campo', 'Campo não computavel');

        $custom_fields = CustomField::select('name')->get();

        foreach ($custom_fields as $custom_field) {
            $fields[] = $custom_field->name;
        }

        return view('columns')->with(array('columns' => $columns, 'fields' => $fields));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function cposts(Request $request)
    {
        $inputs = $request->all();
        $columns = $inputs['columns']['name'];

        $fields = Schema::getColumnListing('datas');
        array_unshift($fields, 'Novo Campo', 'Campo não computavel');

        $action = array();
        foreach ($columns as $key => $column) {
            switch ($fields[$column]) {
                case 'Novo Campo':
                    $custom_field = CustomField::where('name', $key)->get()->first();
                    if (!$custom_field) {
                        $custom_field = CustomField::create( array('name' => $key) );
                    }
                    
                    $columns = ColumnNew::where('column', $key)->get();

                    foreach ($columns as $column) {
                        $custom_field_value['data_id'] = $column->data_id;
                        $custom_field_value['custom_field_id'] = $custom_field->id;
                        $custom_field_value['value'] = $column->value;

                        DataCustomField::create( $custom_field_value );
                    }
                    
                    $columns = ColumnNew::where('column', $key)->delete();
                    break;
                case 'Campo não computavel':
                    $columns = ColumnNew::where('column', $key)->delete();
                    break;
                
                default:
                    $field = $fields[$column];
                    $vocabulary = Taxonomy::getVocabularyByName($field);
                    $term = Term::where('name', $key)->get()->first();

                    if (!$term)
                        Taxonomy::createTerm($vocabulary->id, $key);
                    
                    $columns = ColumnNew::where('column', $key)->get();

                    foreach ($columns as $column) {
                        $data = Data::find($column->data_id);
                        $data->{$field} = $column->value;

                        $data->save();
                    }
                    
                    $columns = ColumnNew::where('column', $key)->delete();
                    break;
            }
        }
        
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Get all the files uploads and they status
        $file = File::find($id);

        return view('show')->with('file', $file);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
