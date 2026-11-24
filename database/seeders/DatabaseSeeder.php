<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\Categoria;
use App\Models\Equipo;
use App\Models\Producto;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Práctica 1.3: Roles y usuarios académicos ---
        $rolProfesor = Role::firstOrCreate(
            ['slug' => 'profesor'],
            ['name' => 'Profesor']
        );

        $rolAlumno = Role::firstOrCreate(
            ['slug' => 'alumno'],
            ['name' => 'Alumno']
        );

        User::firstOrCreate(
            ['email' => 'profesor@sistema.edu'],
            [
                'name' => 'Profesor Demo',
                'password' => 'Profesor1234!',
                'role_id' => $rolProfesor->id,
            ]
        );

        $userAlumno = User::firstOrCreate(
            ['email' => 'alumno@sistema.edu'],
            [
                'name' => 'Alumno Demo',
                'password' => 'Alumno1234!',
                'role_id' => $rolAlumno->id,
            ]
        );

        Alumno::firstOrCreate(
            ['user_id' => $userAlumno->id],
            [
                'nombre' => 'Alumno Demo',
                'faltas' => 1,
                'calificacion' => 92,
            ]
        );

        // --- Práctica 1.4: Roles, usuarios, categorías y productos ---
        $rolAdmin = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Administrador']
        );

        $rolEmpleado = Role::firstOrCreate(
            ['slug' => 'empleado'],
            ['name' => 'Empleado']
        );

        User::firstOrCreate(
            ['email' => 'admin@productos.local'],
            [
                'name' => 'Administrador Productos',
                'password' => 'Admin1234!',
                'role_id' => $rolAdmin->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'empleado@productos.local'],
            [
                'name' => 'Empleado Productos',
                'password' => 'Empleado1234!',
                'role_id' => $rolEmpleado->id,
            ]
        );

        $categorias = [
            'Electrónica',
            'Papelería',
            'Accesorios',
            'Hogar',
            'Cómputo',
        ];

        foreach ($categorias as $nombreCategoria) {
            Categoria::firstOrCreate(['nombre' => $nombreCategoria]);
        }

        $electronica = Categoria::where('nombre', 'Electrónica')->first();
        $computo = Categoria::where('nombre', 'Cómputo')->first();

        if ($electronica && $computo) {
            Producto::firstOrCreate(
                ['nombre' => 'Teclado Mecánico 60%'],
                [
                    'categoria_id' => $computo->id,
                    'descripcion' => 'Teclado compacto de respuesta rápida para desarrollo y uso diario.',
                    'precio' => 1299.00,
                    'existencias' => 14,
                    'estado' => 'Disponible',
                ]
            );

            Producto::firstOrCreate(
                ['nombre' => 'Hub USB-C Multipuerto'],
                [
                    'categoria_id' => $electronica->id,
                    'descripcion' => 'Adaptador con puertos HDMI, USB 3.0 y carga passthrough.',
                    'precio' => 549.50,
                    'existencias' => 0,
                    'estado' => 'Agotado',
                ]
            );
        }

        // --- Práctica 1.5 y 1.6: Roles estudiante/jugador, usuarios y equipos ---
        $rolEstudiante = Role::firstOrCreate(
            ['slug' => 'estudiante'],
            ['name' => 'Estudiante']
        );

        $rolJugador = Role::firstOrCreate(
            ['slug' => 'jugador'],
            ['name' => 'Jugador']
        );

        User::firstOrCreate(
            ['email' => 'admin@reservaciones.local'],
            [
                'name' => 'Administrador General',
                'password' => 'Admin1234!',
                'role_id' => $rolAdmin->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'estudiante1@reservaciones.local'],
            [
                'name' => 'Estudiante Uno',
                'password' => 'Estudiante1234!',
                'role_id' => $rolEstudiante->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'estudiante2@reservaciones.local'],
            [
                'name' => 'Estudiante Dos',
                'password' => 'Estudiante1234!',
                'role_id' => $rolEstudiante->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'jugador1@gato.local'],
            [
                'name' => 'Jugador X',
                'password' => 'Jugador1234!',
                'role_id' => $rolJugador->id,
            ]
        );

        User::firstOrCreate(
            ['email' => 'jugador2@gato.local'],
            [
                'name' => 'Jugador O',
                'password' => 'Jugador1234!',
                'role_id' => $rolJugador->id,
            ]
        );

        $equiposIniciales = [
            [
                'codigo' => 'PC-01',
                'nombre' => 'Estación Lab A-01',
                'ubicacion' => 'Laboratorio 1',
                'especificaciones' => 'Ryzen 5, 16GB RAM, SSD 512GB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-02',
                'nombre' => 'Estación Lab A-02',
                'ubicacion' => 'Laboratorio 1',
                'especificaciones' => 'Ryzen 5, 16GB RAM, SSD 512GB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-03',
                'nombre' => 'Estación Lab A-03',
                'ubicacion' => 'Laboratorio 1',
                'especificaciones' => 'Core i7, 32GB RAM, SSD 1TB',
                'estado' => 'Disponible',
            ],
            [
                'codigo' => 'PC-04',
                'nombre' => 'Estación Lab B-01',
                'ubicacion' => 'Laboratorio 2',
                'especificaciones' => 'Core i5, 16GB RAM, SSD 512GB',
                'estado' => 'Disponible',
            ],
        ];

        foreach ($equiposIniciales as $equipo) {
            Equipo::firstOrCreate(
                ['codigo' => $equipo['codigo']],
                $equipo
            );
        }
    }
}