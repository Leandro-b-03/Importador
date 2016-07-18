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
        require_once base_path('vendor/box/spout/src/Spout/Reader/ReaderFactory.php');
        require_once base_path('vendor/box/spout/src/Spout/Common/Type.php');
        $inputs = $request->all();

        try {
            if($inputs) {
                set_time_limit(0);
                ini_set('max_execution_time',-1);
                ini_set('memory_limit', '-1'); 
                // $cacheMethod = PHPExcel_CachedObjectStorageFactory:: cache_in_memory_gzip;
                // $cacheSettings = array( ' memoryCacheSize ' => '-1');
                // PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

                $name = $inputs['file']->getClientOriginalName();

                // die(d($inputs['file']->getClientOriginalName()));

                $this->file = File::create( array('name' => $name ,false) );
                $this->columns = false;

                if ($this->file) {
                    $reader = \Box\Spout\Reader\ReaderFactory::create(\Box\Spout\Common\Type::XLSX);
                    $reader->open($inputs['file']->getRealPath());

                    foreach ($reader->getSheetIterator() as $sheet) {
                    // Excel::load($inputs['file']->getRealPath(), function($reader) {
                        // Getting all results
                        // $results = $reader->get();

                        // foreach ($results as $sheet) {
                            $folder = Folder::create( array('file_id' => $this->file->id, 'name' => $sheet->getName()) );
                            $keys = null;

                            if ($sheet->getName() == 'IDENTIFICAÇÃO') {
                                foreach ($sheet->getRowIterator() as $row) {
                                    if (is_string ($row[0])) {
                                        if (str_replace(' ', '', strtolower($row[0])) == str_replace(' ', '', strtolower('Projeto / Customizações'))) {
                                            $this->file->project_id = $row[1];

                                            $this->file->save();
                                        }
                                    }
                                }
                            } else {
                                $grupo = '';
                                // foreach ($sheet as $row) {
                                foreach ($sheet->getRowIterator() as $row) {
                                    if (strtolower($row[0]) == 'id') {
                                        foreach ($row as $value) {
                                            $unwanted_array = array('Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A',
                                                'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E',
                                                'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O',
                                                'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
                                                'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e',
                                                'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n',
                                                'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u',
                                                'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y' );
                                            $keys[] = str_replace(' ', '_', str_replace('-', '_', str_replace('/', '_', str_replace('\\', '_', strtolower(strtr( $value, $unwanted_array ))))));
                                        }
                                    } else if (!is_int($row[0])) {
                                        if ($grupo != $row[0])
                                            $grupo = $row[0];
                                    } else {
                                        $_row = array();
                                        $row_count = count($row);

                                        for ($i = 0; $i < $row_count; $i++) {
                                            if (isset($keys[$i]) && $keys[$i] != '')
                                                $_row[$keys[$i]] = $row[$i];
                                        }
                                        
                                        $_row['folder_id'] = $folder->id;
                                        $_row['posicao'] = intval($row[0]);
                                        $_row['grupo'] = $grupo;

                                        $columns = array();
                                        $custom_fields = array();

                                        foreach ($_row as $key => $value) {
                                            gc_collect_cycles();
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
                                                            'posicao' => intval($row[0]),
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
                    //});
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
        $file = File::find($id);

        $file->delete();
    }
}
