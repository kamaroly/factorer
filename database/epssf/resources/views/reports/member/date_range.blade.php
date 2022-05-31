<div class="ml-6 hidden-print">
	
	<input type="hidden" value="{{ $url }}" id="js-report-url">

	<div class="md:flex md:items-center mb-6 ml-4">
	    <div class="md:w-1/5">
	      <label class="block text-green-700 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
	        Start Date
	      </label>
	    </div>
	    <div class="md:w-1/5">
	      <input class="bg-gray-200 appearance-none border-2 
	      				border-gray-200 rounded w-full py-2 px-4 text-gray-700 
	      				leading-tight focus:outline-none focus:bg-white 
	      				focus:border-green-500" type="text" 
	      				value="{{ request()->segment(4) }}"
	      				id="js-start-date" data-toggle="datepicker">
	    </div>
	    <div class="md:w-1/5">
	      <label class="block text-green-700 font-bold md:text-right mb-1 md:mb-0 pr-4">
	        End Date
	      </label>
	    </div>
	    <div class="md:w-1/5">
	      <input class="bg-gray-200 appearance-none border-2 
	      				border-gray-200 rounded w-full py-2 px-4 text-gray-700 
	      				leading-tight focus:outline-none focus:bg-white 
	      				focus:border-green-500"
	      				type="text" value="{{ request()->segment(5) }}"
	      				id="js-end-date" data-toggle="datepicker">
	    </div>

	    <div class="md:w-1/5 ml-2">
    	     <button class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline" type="button" onclick="filterByDate()">
		        Filter
		      </button>
	    </div>
	</div>
</div>