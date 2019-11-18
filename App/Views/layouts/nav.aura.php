@section::nav
<nav class="w-full bg-gray-900">
    <div class="w-full container mx-auto flex flex-wrap items-center justify-between mt-0 py-2">
        <div class="">
            <h1 class="font-semibold tracking-tight text-white logo">kikopolis.tech</h1>
        </div>
        <div class="block lg:hidden pr-4">
            <button id="nav-toggle" class="flex items-center p-1 text-white hover:text-gray-500">
                <svg class="fill-current h-6 w-6" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><title>Menu</title><path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/></svg>
            </button>
        </div>
        <div class="w-full flex-grow lg:flex lg:items-center lg:w-auto hidden lg:block mt-2 lg:mt-0 bg-gray-900 lg:bg-transparent text-black p-4 lg:p-0 z-20" id="nav-content">
            <ul class="list-reset lg:flex justify-end flex-1 items-center">
                <li class="mr-3 inline-block">
                    <a class="btn bg-gray-800 hover:bg-gray-400 text-gray-300 hover:text-gray-900 font-bold py-2 px-4 border-b-4 border-gray-700 hover:border-gray-700 rounded" href="@@urlroot/home">Home</a>
                </li>
                <li class="mr-3 inline-block">
                    <a class="btn bg-gray-800 hover:bg-gray-400 text-gray-300 hover:text-gray-900 font-bold py-2 px-4 border-b-4 border-gray-700 hover:border-gray-700 rounded" href="@@urlroot/contact">Contact</a>
                </li>
                <li class="mr-3 inline-block">
                    <a class="btn bg-gray-800 hover:bg-gray-400 text-gray-300 hover:text-gray-900 font-bold py-2 px-4 border-b-4 border-gray-700 hover:border-gray-700 rounded" href="@@urlroot/faq">F.A.Q</a>
                </li>
                <li class="mr-3 inline-block">
                    <a class="btn bg-gray-800 hover:bg-gray-400 text-gray-300 hover:text-gray-900 font-bold py-2 px-4 border-b-4 border-gray-700 hover:border-gray-700 rounded" href="@@urlroot/docs">Documentation</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
@endsection