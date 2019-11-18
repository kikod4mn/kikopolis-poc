@extends::layouts.base

@extend::base
<h1 class="block w-full text-center">Welcome to kikopolis.tech</h1>
<hr class="style-seven mt-3">
<div class="text-center w-full">
    <p class="w-full text-center">The humble home of the Kikopolis PHP framework and web developer Kristo Leas</p>
</div>
<div class="w-full text-center mx-auto">
    <h3>Contact information</h3>
    <a href="http://www.github.com/kikod4mn" target="_blank"><i class="fap fab fa-github"></i></a>
    <a href="http://facebook.com/kristo.leas" target="_blank"><i class="fap fab fa-facebook"></i></a>
    <a href="@@urlroot/contact"><i class="fap fas fa-at"></i></a>
</div>
<hr class="style-two mt-3">
<div class="w-full flex items-center flex-col mx-auto">
    <h3>Read the documentation</h3>
    <ul class="list-disc mx-auto">
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/config">Configuration options</a></li>
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/routing">Routing system and structure</a></li>
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/users">Users and roles</a></li>
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/aurora">Aurora templates</a></li>
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/forms">Form utility class</a></li>
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/helpers">General info on helpers</a></li>
        <li><a class="btn text-gray-500 hover:text-gray-200" href="@@urlroot/docs/utility">General info on utility classes</a></li>
    </ul>
</div>


@endextend