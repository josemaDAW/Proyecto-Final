<?php

namespace App\Http\Controllers;

use App\Models\Productos;
use App\Models\Categorias;
use App\Models\Opciones;
use App\Models\Items;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index() {
        $productosList = Productos::recuperarProductosFront();
        $categoriasList = Categorias::orderBy('name')->get();
        $opciones = Opciones::convertToArray();
        return view('front.front', ['home'=>true, 'productosList'=>$productosList, 'categoriasList'=>$categoriasList,'opciones' => $opciones]);
    }

    public function show($id) {
        $p = Productos::find($id);
        $data['productos'] = $p;
        $categoria = Categorias::find($r->idCategoria);
        return view('categorias.show', $data);
    }

    /* Muestra todos los productos de una categoría. Si la categoría tiene un ítem destacado, muestra un selector
       con todos los valores de ese ítem */
    public function mostrarCategorias($id, Request $r) {
        $categoria = Categorias::find($id);
        $destacados = $categoria->items()->where('destacado', 1)->get();
        if (blank($r->textoBusqueda) && count($destacados) > 0) {
            // Si algún ítem de esta categoría está marcado como "destacado", se mostrará una vista para elegir
            // entre todos los valores de ese ítem. Excepto si estamos usando el buscador, en cuyo caso lanzaremos la búsqueda
            // en el else ignorando el ítem destacado.

            // Recuperamos todos los valores del item destacado
            $idItemDestacado = $destacados[0]->id;
            $valores = Items::recuperarValores($idItemDestacado);
            $data['valores'] = $valores;
            $data['categoria'] = $categoria;
            $data['idItem'] = $idItemDestacado;
            $data['opciones'] = Opciones::convertToArray();
            $data['categoriasList'] = Categorias::orderBy('name')->get();
            return view('front.categorias_destacados', $data);
        }
        else {
            // Si no hay ningún producto destacado en esta categoría, se mostrarán todos los productos de la categoría.
            $data['idCategoria'] = $id;
            $data['txt'] = $r->textoBusqueda;

            $categoriasList = Categorias::orderBy('name')->get();
            $todosProductos = blank($r->textoBusqueda) ? Productos::recuperarPorCategoria($id) : Productos::buscador($data);
            $opciones = Opciones::convertToArray();
            $msg = count($todosProductos) > 0 ? null : 'No hay resultados de búsqueda';
            return view('front.piezas_categorias', ['msg'=> $msg,'todosProductos'=>$todosProductos,'categoriasList'=>$categoriasList,'categoria' => $categoria,
            'textoBusqueda' => $r->textoBusqueda, 'opciones' => $opciones]);    
        }
    }

    /* Muestra todos los productos de una categoría, filtrados por el valor de un ítem destacado */
    public function vistaPorItemDestacado($idCategoria, $idItem, $valueItem) {
        if ($idItem == -1) {
            // Si el id del item destacado es -1, se mostrarán todos los productos de la categoría (sin filtrar por ítem)
            $categoria = Categorias::find($idCategoria);
            $categoriasList = Categorias::orderBy('name')->get();
            $todosProductos = Productos::recuperarPorCategoria($idCategoria);
            $opciones = Opciones::convertToArray();
            $msg = count($todosProductos) > 0 ? null : 'No hay resultados de búsqueda';
            return view('front.piezas_categorias', ['msg'=> $msg,'todosProductos'=>$todosProductos,'categoriasList'=>$categoriasList,'categoria' => $categoria,
            'opciones' => $opciones]);    
        } 
        else {
            // Si el id del item destacado es distinto de -1, se mostrarán todos los productos de la categoría que tengan el valor seleccionado en $valueItem
            $categoria = Categorias::find($idCategoria);
            $categoriasList = Categorias::orderBy('name')->get();
            $todosProductos = Productos::recuperarPorItemDestacado($idCategoria, $idItem, $valueItem);
            $opciones = Opciones::convertToArray();
            $msg = count($todosProductos) > 0 ? null : 'No hay resultados de búsqueda';
            return view('front.piezas_categorias', ['msg'=> $msg,'todosProductos'=>$todosProductos,'categoriasList'=>$categoriasList,'categoria' => $categoria,
            'opciones' => $opciones]);    
        }
    }


    /*Funcion buscador categorías para que solo funcione en la vista de la categoria seleccionada*/
    public function buscadorCategorias(Request $r) {
        $categoria = Categorias::find($r->idCategoria);
        $data['idCategoria'] = $r->idCategoria;
        $data['txt'] = $r->textoBusqueda;

        $categoriasList = Categorias::orderBy('name')->get();
        $todosProductos = blank($r->textoBusqueda) ? Productos::recuperarPorCategoria($id) : Productos::buscador($data);
        $opciones = Opciones::convertToArray();
        $msg = count($todosProductos) > 0 ? null : 'No hay resultados de búsqueda';
        return view('front.piezas_categorias', ['productosList'=>$productosList, 'categoriasList'=>$categoriasList, 'opciones' => $opciones, 
                    'msg'=> $msg,'textoBusqueda'=> $r->textoBusqueda, 'todosProductos'=>$todosProductos,
                    'categoriasList'=>$categoriasList, 'idCategoria' => $r->idCategoria, 'categoria' => $categoria]);
    }

    /*Funcion vista buscador prepara todas las categorías e items para mostrarlos en la página del buscador*/
    public function vistaBuscador(Request $r) {
        $categoriasList = Categorias::orderBy('name')->get();
        $opciones = Opciones::convertToArray();
        return view('front.buscador', ['categoriasList'=>$categoriasList, 'textoBusqueda' => $r->textoBusqueda, 'opciones' => $opciones]);
    }

    /*Funcion buscador general front*/ 
    public function buscadorGeneral(Request $r) {
        $data['txt'] = $r->textoBusqueda;
        $data['page'] = $r->page;
        $categoriasList = Categorias::orderBy('name')->get();
        
        $todosProductos =  Productos::buscador($data);
        $opciones = Opciones::convertToArray();
        $msg = count($todosProductos) > 0 ? null : 'No hay resultados de búsqueda';       
        return view('front.piezas_categorias', ['textoBusqueda'=> $r->textoBusqueda, 'msg'=> $msg, 'todosProductos'=>$todosProductos,
                    'categoriasList'=>$categoriasList, 'textoBusqueda' => $r->textoBusqueda, 'opciones' => $opciones]);
    }


    /*Funcion por campos según categoría front*/ 
    public function buscadorPorCampos(Request $r) {
        /*Si $r->page es null al convertirlo en intval es 0 y si es 0 por default es 1 */
        $currentPage = intval($r->page) == 0 ? 1 : intval($r->page);
        /*Cada cuanto pagina*/
        $pagination = 8;
        $data['categoria_id'] = $r->categoria_id;
        $data['items'] = $r->items;

        $productosList = Productos::buscador($data);
        /*El numero de pagina que tiene los productos  -Ceil redondea hacia arriba- */
        $pages = ceil(count($productosList->get()) / $pagination);
        /*Te hace un get de los productos y se salta los productos de la pagian en la que estas y coge el numero de productos que tiene la paginacion ($currentPage-1)  */
        // for ($i = 0; $i < ($currentPage - 1) * $pagination; $i++) {
            $todosProductos =  $productosList->skip(($currentPage - 1) * $pagination)->take($pagination)->get();
        // }

        $categoriasList = Categorias::orderBy('name')->get();
        $opciones = Opciones::convertToArray();
        $msg = count($productosList->get()) > 0 ? null : 'No hay resultados de búsqueda';
        return view('front.piezas_categorias', ['categoria_id' => $r->categoria_id,'msg'=> $msg,'currentPage' => $currentPage, 
                    'pages' => $pages, 'items' => $r->items, 'textoBusqueda'=> $r->textoBusqueda, 
                    'opciones' => $opciones, 'todosProductos'=>$todosProductos, 'categoriasList'=>$categoriasList]);
    }

    // Muestra la vista de "acerca de"
    public function acercaDe() {
        $opciones = Opciones::convertToArray();
        $categoriasList = Categorias::orderBy('name')->get();
        return view('front.acerca_de', ['opciones' => $opciones, 'categoriasList'=>$categoriasList]);
    }

    // Muestra la vista de "política de privacidad"
    public function politicaPrivacidad() {
        $opciones = Opciones::convertToArray();
        $categoriasList = Categorias::orderBy('name')->get();
        return view('front.politica_privacidad', ['opciones' => $opciones, 'categoriasList'=>$categoriasList]);
    }

    // Muestra la vista de "política de cookies"
    public function politicaCookies() {
        $opciones = Opciones::convertToArray();
        $categoriasList = Categorias::orderBy('name')->get();
        return view('front.politica_cookies', ['opciones' => $opciones, 'categoriasList'=>$categoriasList]);
    }

    // Muestra la vista de "términos de uso"
    public function terminosUso() {
        $opciones = Opciones::convertToArray();
        $categoriasList = Categorias::orderBy('name')->get();
        return view('front.terminos_uso', ['opciones' => $opciones, 'categoriasList'=>$categoriasList]);
    }

}