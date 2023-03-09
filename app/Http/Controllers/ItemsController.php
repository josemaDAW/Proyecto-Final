<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use App\Models\Categorias;

class ItemsController extends Controller
{

    public function __construct() {
        $this->middleware("auth");
    }

    public function index($idCategoria = null) {
        if ($idCategoria == null)
            // Si no nos dicen categoría, mostramos la primera de la tabla
            $idCategoria = Categorias::min('id');
        $itemsList = Items::where('categoria_id', $idCategoria)->orderBy('order')->get();
        $categorias = Categorias::orderBy('name')->get();
        return view('items.all', ['itemsList'=>$itemsList, 'categorias'=>$categorias, 'idCategoria'=>$idCategoria]);
    }

    public function show($id) {
        $p = Items::find($id);
        $data['items'] = $p;
        return view('items.show', $data);
    }

    public function create() {
        $categorias = Categorias::orderBy('name')->get();
        return view('items.form', ['categoriasList' => $categorias]);
    }

    public function store(Request $r) {
        $p = new Items();
        $p->name = $r->name;
        $p->categoria_id = $r->categoria_id;
        // Obtenemos el máximo order de la categoria y asignamos el siguiente al nuevo registro
        $maxOrder = Items::where('categoria_id', $r->categoria_id)->max('order');
        $p->order = $maxOrder + 1;
        $p->save();
        return redirect()->route('items.indexPorCategoria', $r->categoria_id);
    }

    public function edit($id) {
        $items = Items::find($id);
        $categorias = Categorias::orderBy('name')->get();
        return view('items.form', array('item' => $items, 'categoriasList' => $categorias));
    }

    public function update($id, Request $r) {
        $p = Items::find($id);
        $p->name = $r->name;
        $p->categoria_id = $r->categoria_id;
        $p->save();
        return redirect()->route('items.indexPorCategoria', $r->categoria_id);
    }

    public function destroy($id) {
        $p = Items::find($id);
        $p->delete();
        return redirect()->route('items.indexPorCategoria', $p->categoria_id);
    }

    // Cambia el orden de un ítem en una categoría.
    // Recibe como parámetros el id del item, el orden actual y la cantidad de posiciones a cambiar, que puede ser -1 o 1.
    // Si $cantidad == -1, se permuta el item con el que tenga un orden justo menor.
    // Si $cantidad == 1, se permuta el item con el que tenga un orden justo mayor.
    public function cambiarOrden($id, $orden, $cantidad) {
        $item = Items::find($id);
        $categoria_id = $item->categoria_id;
        $order = $item->order;
        if ($cantidad == -1) {
            $itemAnterior = Items::where('categoria_id', $categoria_id)->where('order', '=', $order-1)->first();
            if ($itemAnterior) {
                $item->order = $itemAnterior->order;
                $item->save();
                $itemAnterior->order = $order;
                $itemAnterior->save();
            }
        } else {
            $itemSiguiente = Items::where('categoria_id', $categoria_id)->where('order', '=', $order+1)->first();
            if ($itemSiguiente) {
                $item->order = $itemSiguiente->order;
                $item->save();
                $itemSiguiente->order = $order;
                $itemSiguiente->save();
            }
        }
        return redirect()->route('items.indexPorCategoria', $categoria_id);
    }
}