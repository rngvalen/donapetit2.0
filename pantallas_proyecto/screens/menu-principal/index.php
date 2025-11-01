<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenido - Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'custom-green': 'rgb(var(--custom-green) / <alpha-value>)',
                        'light-green': 'rgb(var(--light-green) / <alpha-value>)',
                        'custom-yellow': 'rgb(var(--custom-yellow) / <alpha-value>)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans flex flex-col items-center min-h-screen">

    <div class="bg-custom-green text-white w-full py-6 flex items-center justify-between px-4">
        <div class="flex-1"></div>
        <h1 class="text-2xl font-bold flex-1 text-center">Bienvenido SPIRIX!!</h1>
        <div class="flex-1 flex justify-end">
            <button id="menu-toggle" class="focus:outline-none z-50 p-4 rounded cursor-pointer bg-white bg-opacity-20">
                <div class="w-14 h-14 flex flex-col justify-center items-end">
                    <span class="block w-9 h-0.5 bg-white rounded mb-1"></span>
                    <span class="block w-9 h-0.5 bg-white rounded mb-1"></span>
                    <span class="block w-9 h-0.5 bg-white rounded"></span>
                </div>
            </button>
        </div>
    </div>


    <div id="dropdown" class="hidden fixed top-24 right-4 bg-white shadow-lg rounded-lg py-2 w-48 z-50 border border-gray-200">
        <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-t-lg">
            <span class="mr-3 text-xl">👤</span>Mi Perfil
        </a>
        <a href="#" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
            <span class="mr-3 text-xl">⚙️</span>Configuración
        </a>
        <button class="w-full text-left px-4 py-2 text-red-600 hover:bg-red-50 rounded-b-lg">
            Cerrar sesión
        </button>
    </div>


    <div class="relative w-full max-w-6xl h-96 overflow-hidden border-2 border-custom-green mx-auto my-5 bg-white rounded-lg">
        <div class="carousel flex flex-nowrap transition-transform duration-500 ease-in-out h-full" style="width: 110%; transform: translateX(0%);">

            <div class="flex-none w-full flex items-center justify-center bg-light-green relative">
                <img src="images/imagen1.jpg" alt="Slide 1 - Product/Image 1" class="w-full h-full object-cover">


            </div>
            <div class="flex-none w-full flex items-center justify-center bg-light-green relative">
                <img src="images/imagen2.jpg" alt="Slide 2 - Product/Image 2" class="w-full h-full object-cover">


            </div>
            <div class="flex-none w-full flex items-center justify-center bg-light-green relative">
                <img src="images/imagen3.jpg" alt="Slide 3 - Product/Image 3" class="w-full h-full object-cover">


            </div>
            <div class="flex-none w-full flex items-center justify-center bg-light-green relative">
                <img src="images/imagen4.jpg" alt="Slide 4 - Product/Image 4" class="w-full h-full object-cover">


            </div>
        </div>


        <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2 z-10">
            <button class="w-3 h-3 bg-gray-300 rounded-full indicator active:bg-custom-green transition-colors" onclick="goToSlide(0)"></button>
            <button class="w-3 h-3 bg-gray-300 rounded-full indicator" onclick="goToSlide(1)"></button>
            <button class="w-3 h-3 bg-gray-300 rounded-full indicator" onclick="goToSlide(2)"></button>
            <button class="w-3 h-3 bg-gray-300 rounded-full indicator" onclick="goToSlide(3)"></button>
        </div>


        <button class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 p-2 rounded-full text-custom-green text-2xl font-bold hover:bg-opacity-75 z-10" onclick="prevSlide()">‹</button>
        <button class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-50 p-2 rounded-full text-custom-green text-2xl font-bold hover:bg-opacity-75 z-10" onclick="nextSlide()">›</button>
    </div>


    <div class="flex flex-col items-center w-full max-w-4xl my-8 space-y-4">
        <a href="" class="bg-custom-green text-white px-16 py-6 rounded-lg hover:bg-green-600 text-xl font-semibold shadow-lg min-w-[300px] border-2 border-white w-full max-w-[400px] block text-center no-underline">
            Cargar producto
        </a>
        <a href="" class="bg-custom-green text-white px-16 py-6 rounded-lg hover:bg-green-600 text-xl font-semibold shadow-lg min-w-[300px] border-2 border-white flex items-center justify-center w-full max-w-[400px] no-underline">
            <span class="text-2xl mr-2">+</span>Mis productos
        </a>
        <a href="" class="bg-custom-green text-white px-16 py-6 rounded-lg hover:bg-green-600 text-xl font-semibold shadow-lg min-w-[300px] border-2 border-white flex items-center justify-center w-full max-w-[400px] no-underline">
            <span class="text-2xl mr-2">📈</span>Estadisticas
        </a>
    </div>

    <script src="carousel.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('JS loaded');
            const toggle = document.getElementById('menu-toggle');
            const dropdown = document.getElementById('dropdown');
            console.log('Toggle element:', toggle);
            console.log('Dropdown element:', dropdown);
            if (toggle && dropdown) {
                toggle.addEventListener('click', function(e) {
                    console.log('Toggle clicked');
                    e.stopPropagation();
                    dropdown.classList.toggle('hidden');
                });

                document.addEventListener('click', function(event) {
                    if (!toggle.contains(event.target) && !dropdown.contains(event.target)) {
                        dropdown.classList.add('hidden');
                    }
                });
            } else {
                console.log('Elements not found');
            }
        });
    </script>
</body>
</html>
