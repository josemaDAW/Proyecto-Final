<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos_etiquetas', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('productos_id');
            $table->unsignedBigInteger('etiquetas_id');

            $table->foreign('productos_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('etiquetas_id')->references('id')->on('etiquetas')->onDelete('cascade');

            $table->timestamps();
        });
    }


    public function store(Request $r) {
        $etiquetas = new Etiquetas($r→all());
        $etiquetas->productos()->attach($r->productos);
        $etiquetas->save();
    }
    
    public function update(Request $r, $id) {
        $etiquetas = Etiquetas::find($id);
        $etiquetas->fill($r->all());
        $etiquetas->productos()->sync($r->productos);
        $etiquetas->save();
    }
       
    public function destroy($id) {
        $etiquetas = Etiquetas::find($id);
        $etiquetas->productos()->detach();
        $etiquetas->delete();
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productos_etiquetas');
    }
};
