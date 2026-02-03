<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {

            // Estado de la tarea (backlog, in_progress, done, etc)
            $table->string('status')
                ->default('backlog')
                ->after('done');

            // Prioridad visual
            $table->string('priority')
                ->default('medium')
                ->after('status');

            // Fecha límite
            $table->dateTime('due_date')
                ->nullable()
                ->after('priority');

            // Responsable de la tarea
            $table->string('assigned_to')
                ->nullable()
                ->after('due_date');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'priority',
                'due_date',
                'assigned_to'
            ]);
        });
    }
};

