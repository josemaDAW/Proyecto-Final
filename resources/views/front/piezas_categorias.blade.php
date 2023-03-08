@extends('layouts.front')
@section('content')
<div id="page-top">
        <section class="page-section bg-light" id="portfolio"
            style="--paginacion: {{ $opciones['paginacion_color'] }}; background-color: {{ $opciones['color_fondo'] }}!IMPORTANT;">

            <div class="" style="font-family: {{$opciones['tipografia3']}}">
                <div class="grid">
                    @if (isset($msg) && !blank($msg))
                    <div class="text-center">
                        {{$msg}}
                    </div>
                    @endif
                    <!-- Pintando las cajas de los productos -->
                    @foreach($todosProductos as $key => $producto)
                    <div class="gridItem">

                        <div class="portfolio-item">
                            <a class="portfolio-link" data-bs-toggle="modal" href="#producto{{$producto->id}}">
                                <div class="portfolio-hover">
                                    <div class="portfolio-hover-content"><i class="fas fa-plus fa-3x"></i></div>
                                </div>
                                <img class="img-fluid" src='{{asset("storage/$producto->id/mini_$producto->image")}}'
                                    width="auto">
                            </a>
                            <div class="portfolio-caption">
                                <div class="portfolio-caption-heading">{{$producto->name}}</div>
                            </div>
                        </div>
                    </div>
                    <!-- Creando los cuadros modales de cada prodcto -->
                    <div class="portfolio-modal modal fade" id="producto{{$producto->id}}" tabindex="-1" role="dialog"
                        aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="close-modal" data-bs-dismiss="modal"><svg id="Layer_1" data-name="Layer 1"
                                        viewBox="0 0 579.74 579.74">
                                        <defs>
                                            <style>
                                            .cls-1 {
                                                fill: none;
                                                stroke: #000;
                                                stroke-miterlimit: 10;
                                                stroke-width: 6px;
                                            }
                                            </style>
                                        </defs>
                                        <line class="cls-1" x1="2.12" y1="2.12" x2="577.62" y2="577.62" />
                                        <line class="cls-1" x1="2.12" y1="577.62" x2="577.62" y2="2.12" />
                                    </svg></div>
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-12">
                                            <div class="modal-body">
                                                <!-- Project details-->
                                                <h2 class="title text-uppercase pb-4">{{$producto->name}}</h2>
                                                <div id="carouselExampleIndicators{{$key}}"
                                                    class="carousel carousel-dark slide" data-bs-ride="true">
                                                    <!-- Indicadores de las imagenes (flechas) -->
                                                    <div class="carousel-indicators">
                                                        <button type="button"
                                                            data-bs-target="#carouselExampleIndicators{{$key}}"
                                                            data-bs-slide-to="0" class="active"
                                                            aria-label="Slide 0"></button>
                                                        @foreach($producto->imagenes as $index => $image)
                                                        <button type="button"
                                                            data-bs-target="#carouselExampleIndicators{{$key}}"
                                                            data-bs-slide-to="{{$index + 1}}"
                                                            aria-label="Slide {{$index + 1}}"></button>
                                                        @endforeach
                                                    </div>
                                                    <div class="carousel-inner">
                                                        <!-- Imagen principal -->
                                                        <div class="carousel-item active w-100">
                                                            <!-- Botones de descarga e impresión de la imagen principal -->
                                                            <div class="d-flex justify-content-center" style="padding-bottom: 5px">
                                                                <button class="btn btn-outline-secondary fa-solid fa-print mt-3" onclick="imprimir('{{json_encode($producto)}}', 'mi_imagen{{$key}}', '{{json_encode($producto->items)}}', '{{$producto->categoria->name}}')">
                                                                <button class="btn btn-outline-secondary fa-solid fa-download mt-3" onclick="download('{{asset("storage/$producto->id/$producto->image")}}','{{$producto->image}}')">
                                                            </div>   
                                                            <!-- Imagen -->                                                    
                                                            <img id="mi_imagen{{$key}}" class="center-block w-40"
                                                                src='{{asset("storage/$producto->id/mini_$producto->image")}}'
                                                                alt="{{$producto->image}}" height="500" />
                                                        </div>

                                                        <!-- Imagenes secundarias -->
                                                        @foreach($producto->imagenes as $key=>$image)
                                                        <div class="carousel-item">
                                                            <!-- Botones de descarga e impresión de la imagen secundaria -->
                                                            <div class="d-flex justify-content-center"  style="padding-bottom: 5px">
                                                                <button class="btn btn-outline-secondary fa-solid fa-print mt-3" onclick="imprimir('{{json_encode($producto)}}', 'img_secundaria_{{$producto->id}}_{{$key}}', '{{json_encode($producto->items)}}', '{{$producto->categoria->name}}')">
                                                                <button class="btn btn-outline-secondary fa-solid fa-download mt-3" onclick="download('{{ asset("storage/$producto->id/$image->image")}}' , '{{$image->image}}' )">
                                                            </div>
                                                            <!-- Imagen -->
                                                            <img id="img_secundaria_{{$producto->id}}_{{$key}}"
                                                                src='{{asset("storage/$producto->id/mini_$image->image")}}'
                                                                class="center-block" height="500"
                                                                alt="{{$image->image}}">
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    <button class="carousel-control-prev" type="button"
                                                        data-bs-target="#carouselExampleIndicators{{$key}}"
                                                        data-bs-slide="prev">
                                                        <span class="carousel-control-prev-icon"
                                                            aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#carouselExampleIndicators{{$key}}"
                                                        data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"
                                                            aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                </div>

                                                <div class='items' id='item{{$producto->id}}' style="padding-left: 25%; padding-right: 20%; text-align: left">
                                                    @foreach ($producto->items as $item)
                                                    <strong>{{$item->name}}:</strong> {{$item->pivot->value}}<br>
                                                    @endforeach
                                                    <!--
                                                    <button class="btn btn-outline-secondary fa-solid fa-print mt-3" onclick="javascript:window.print()">
                                                    <button class="btn btn-outline-secondary fa-solid fa-download mt-3" onclick="download('{{asset("storage/$producto->id/$producto->image")}}','{{$producto->image}}')">
                                                    -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    @endforeach
                    <!--FIN Pintando los productos con su modal -->
                </div>

                <div class="d-flex justify-content-center">
                    @if(isset($pages))
                    <form action="{{route('buscadorPorCampos')}}" method="POST">
                        @csrf
                        <input type="hidden" name="categoria_id" value="{{$categoria_id}}">
                        @foreach($items as $key => $item)
                        <input type="hidden" name="items[{{$key}}]" value="{{$item}}">
                        @endforeach
                        <nav>
                            <ul class="pagination">
                                <li class="page-item {{$currentPage == 1 ? 'disabled' : ''}}">
                                    <button class="page-link" rel="next" aria-label="« Previous" name="page"
                                        value="{{$currentPage-1}}">‹</a>
                                </li>
                                @for($i = 1; $i <= $pages; $i++) <li>
                                    class="page-item {{$currentPage == $i ? 'active' : ''}}">
                                    <button class="page-link" name="page" {{$currentPage == $i ? 'active' : ''}}
                                        value="{{$i}}">{{$i}}</button>
                                    </li>
                                    @endfor
                                    <li class="page-item {{$currentPage == $pages ? 'disabled' : ''}}">
                                        <button class="page-link" rel="next" aria-label="Next »" name="page"
                                            value="{{$currentPage+1}}">›</a>
                                    </li>
                            </ul>
                        </nav>
                    </form>
                    @else
                    {{$todosProductos->links()}}
                    @endif
                </div>
            </div>
</div>
</section>
<!-- FOOTER -->
@endsection

<!-- Librerías para crear PDFs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<!-- Mis scripts -->
<script>

    window.jsPDF = window.jspdf.jsPDF;      // Debe ser una variable global para que funcione html2canvas

    // Genera un PDF con los datos del producto y la imagen del carrusel.
    // Recibe como parámetros el JSON del producto, la URL  de la imagen, el ID de la imagen en el árbol DOM, un JSON con los items del producto y el nombre de la categoría.
    function imprimir(json_product, image_id, json_items, category) {
        // Convertimos los JSON a objetos
        var product = JSON.parse(json_product);
        var items = JSON.parse(json_items);

        // Creamos un documento PDF en blanco
        var doc = new jsPDF();
        window.html2canvas = html2canvas;
        console.log(product);

        // Creamos la cabecera del documento con el títoulo y el subtítulo de la homepage
        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
        doc.text("{{$opciones['home_titulo']}}" + " " + "{{$opciones['home_subtitulo']}}", 20, 12, { align: 'left' });

        // Añadimos el nombre del producto como cabecera del documento PDF
        doc.setFont("helvetica", "bold");
        doc.setFontSize(14);
        doc.text(product.name, 20, 20, { align: 'left' });

        // Calculamos las dimensiones que debe tener la imagen en el PDF
        var width = document.getElementById(image_id).naturalWidth;     // Dimensiones reales de la imagen (en píxeles)
        var height = document.getElementById(image_id).naturalHeight;
        var anchuraImgEnDoc = 0;                                        // Dimensiones que debe tener la imagen en el PDF (en unidades)
        var alturaImgEnDoc = 0;
        if (width > height) {
            anchuraImgEnDoc = 160;   // Si la imagen es más ancha que alta, limitamos la anchura a 160 unidades en el PDF para que no se salga de la página
            alturaImgEnDoc = (anchuraImgEnDoc * height) / width;
        } else {
            alturaImgEnDoc = 160;    // Si la imagen es más alta que ancha, limitamos la altura a 160 unidades en el PDF para que no se salga de la página
            anchuraImgEnDoc = (alturaImgEnDoc * width) / height;
        }

        // Añadimos la imagen del producto
        doc.addImage(document.getElementById(image_id).src, 'JPEG', 20, 30, anchuraImgEnDoc, alturaImgEnDoc);

        // Añadimos el nombre de la categoría
        doc.setFont("helvetica", "normal");
        doc.setFontSize(10);
        doc.text("Categoría: " + category, 20, alturaImgEnDoc + 40, { align: 'left' });

        // Recorremos todos los items del producto y los enviamos al PDF        
        var y = alturaImgEnDoc + 48;
        for (var i = 0; i < items.length; i++) {
            doc.setFont("helvetica", "normal");
            doc.setFontSize(10);
            doc.text(items[i].name + ": " + items[i].pivot.value, 20, y, { align: 'left' });
            y += 8;

        }
        doc.save(product.name + '.pdf');    // Forzamos la descarga del PDF
    }

    // Descarga la imagen del producto como un archivo. 
    // Forzamos la descarga mediante Javascript para evitar que el navegador la abra en una nueva pestaña.
    function download(url_file, filename) {
        const link = document.createElement('a');
        link.href = url_file;
        link.setAttribute('download', filename);
        link.click();
    }

</script>
