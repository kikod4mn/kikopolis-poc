@extends::layouts.base

@extend::base
<form action="@@urlroot/send-email" method="post" class="w-full mx-auto p-8">
    <?php echo \Kikopolis\App\Utility\Form::csrf(); ?>
    <input type="hidden" value="POST">
    <div class="md:flex md:items-center mb-6">
        <div class="md:w-1/3">
            <label class="block font-bold md:text-right mb-1 md:mb-0 pr-4" for="subject">Subject</label>
        </div>
        <div class="md:w-2/3">
            <input name="subject" class="block bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full lg:w-2/3 py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" id="subject" type="text" placeholder="Enter message title">
        </div>
    </div>
    <div class="md:flex md:items-center mb-6">
        <div class="md:w-1/3">
            <label class="block font-bold md:text-right mb-1 md:mb-0 pr-4" for="message">Message</label>
        </div>
        <div class="md:w-2/3">
            <textarea name="message" class="block bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full lg:w-2/3 py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-blue-500" id="message" type="text" rows="10"></textarea>
        </div>
    </div>
    <div class="md:flex md:items-center mb-6">
        <div class="md:w-1/3">
        </div>
        <div class="md:w-2/3">
            <button class="btn bg-gray-800 hover:bg-gray-400 text-gray-300 hover:text-gray-900 font-bold py-2 px-4 border-gray-700 hover:border-gray-700 rounded" type="submit" name="submit" value="submit">Send message</button>
        </div>
    </div>
</form>
@endextend