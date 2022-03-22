<div class="row">
    {{-- ('item_name
('item_sku
('grouping
('item_qty
('item_buying_price
('item_selling_price --}}
  
    <div class="col-4">
        <div class="form-group">
            <?php
            $field_name = 'item_name';
            $field_lable = __("receivings::$module_name.$field_name");
            $field_placeholder = __("Select an option");
            $required = "required";
            $select_options = [
                'Tangawize'=>'Tangawize',
                'Ketchou'=>'Ketchou',
                'Majani Chai'=>'Majani Chai',
                'Sucre'=>'Sucre',
                'Miel'=>'Miel',
                'Bouteille de 300ml'=>'Bouteille de 300ml',
                'Bouteille de 500ml'=>'Bouteille de 500ml',
                'Bouteille Vin'=>'Bouteille Vin',
                'Dekalote'=>'Dekalote',
                'Sachet'=>'Sachet',
                'Etiquettes  300ml'=>'Etiquettes  300ml',
                'Etiquettes 500ml'=>'Etiquettes 500ml',

            ];
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->select($field_name, $select_options)->placeholder($field_placeholder)->class('form-control select2')->attributes(["$required"]) }}
        </div>
    </div>

    <div class="col-4">
        <div class="form-group">
            <?php
            $field_name = 'item_qty';
            $field_lable = __("receivings::$module_name.$field_name");
            $field_placeholder = __("receivings::$module_name.$field_name"."_placeholder");
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required", "id" => "js-quantity",  "onkeyup" => "handlePriceAndQuantityChange()"])->value('1') }}

        </div>
    </div>
      <div class="col-4">
        <div class="form-group">
            <?php
            $field_name = 'item_type';
            $field_lable = __("receivings::$module_name.$field_name");
            $field_placeholder = __("Select an option");
            $required = "required";
            $select_options = [
                'Kg'=>'Kg',
                'Pce'=>'Pce',

            ];
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->select($field_name, $select_options)->placeholder($field_placeholder)->class('form-control select2')->attributes(["$required"]) }}
        </div>
    </div>

</div>
<div class="row">
     <div class="col-4">
        <div class="form-group">
            <?php
            $field_name = 'item_buying_price';
            $field_lable = __("receivings::$module_name.$field_name");
            $field_placeholder = __("receivings::$module_name.$field_name"."_placeholder");
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required", "id" => "js-buying-price", "onkeyup" => "handlePriceAndQuantityChange()"]) }}
        </div>
    </div>   
    <div class="col-4">
        <div class="form-group">
            <?php
            $field_name = 'item_total';
            $field_lable = __("receivings::$module_name.$field_name");
            $field_placeholder = __("receivings::$module_name.$field_name"."_placeholder");
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required", "id" => "js-total"]) }}

        


        </div>
    </div>
        <div class="col-4">
        <div class="form-group">
            <?php
            $field_name = 'item_comment';
            $field_lable = __("receivings::$module_name.$field_name");
            $field_placeholder = __("receivings::$module_name.$field_name"."_placeholder");
            $required = "";
            ?>
            {{ html()->label($field_lable, $field_name) }} {!! fielf_required($required) !!}
            {{ html()->text($field_name)->placeholder($field_placeholder)->class('form-control')->attributes(["$required"]) }}
        </div>
    </div>
    
</div>


<!-- Select2 Library -->
<x-library.select2 />
<x-library.datetime-picker />

@push('after-styles')
<!-- File Manager -->
<link rel="stylesheet" href="{{ asset('vendor/file-manager/css/file-manager.css') }}">
@endpush

@push ('after-scripts')
<script type="text/javascript">
$(document).ready(function() {
    $('.select2-category').select2({
        theme: "bootstrap",
        placeholder: '@lang("Select an option")',
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: '{{route("backend.categories.index_list")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });

    $('.select2-tags').select2({
        theme: "bootstrap",
        placeholder: '@lang("Select an option")',
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
            url: '{{route("backend.tags.index_list")}}',
            dataType: 'json',
            data: function (params) {
                return {
                    q: $.trim(params.term)
                };
            },
            processResults: function (data) {
                return {
                    results: data
                };
            },
            cache: true
        }
    });
});
</script>


<script>



    function handlePriceAndQuantityChange()
    {
        const quantity = document.getElementById("js-quantity").value;
        const price = document.getElementById("js-buying-price").value;
        const totalPrice = quantity * price;

        document.getElementById("js-total").value = (totalPrice);
    }

    
</script>


<!-- Date Time Picker & Moment Js-->
<script type="text/javascript">
$(function() {
    $('.datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm:ss',
        icons: {
            time: 'far fa-clock',
            date: 'far fa-calendar-alt',
            up: 'fas fa-arrow-up',
            down: 'fas fa-arrow-down',
            previous: 'fas fa-chevron-left',
            next: 'fas fa-chevron-right',
            today: 'far fa-calendar-check',
            clear: 'far fa-trash-alt',
            close: 'fas fa-times'
        }
    });
});
</script>

<script type="text/javascript" src="{{ asset('vendor/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript" src="{{ asset('vendor/file-manager/js/file-manager.js') }}"></script>

<script type="text/javascript">

CKEDITOR.replace('content', {filebrowserImageBrowseUrl: '/file-manager/ckeditor', language:'{{App::getLocale()}}', defaultLanguage: 'en'});

document.addEventListener("DOMContentLoaded", function() {

  document.getElementById('button-image').addEventListener('click', (event) => {
    event.preventDefault();

    window.open('/file-manager/fm-button', 'fm', 'width=800,height=600');
  });
});

// set file link
function fmSetLink($url) {
  document.getElementById('featured_image').value = $url;
}
</script>
@endpush
