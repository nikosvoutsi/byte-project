<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->timestamps();
        });

        // Insert default values
        DB::table('jobs')->insert([
            ['title' => 'PHP Developer'],
            ['title' => 'JAVA Developer'],
            ['title' => 'PYTHON Developer'],
            ['title' => 'ERP Support'],
            ['title' => 'Sales'],
            ['title' => 'Technician'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
