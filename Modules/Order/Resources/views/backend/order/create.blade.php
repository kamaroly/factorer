<div class="form-group">
    <label for="properties">Order Items</label>
    <div class="row">
        <div class="col-md-2">
            #
        </div>
        <div class="col-md-3">
            Item
        </div>
         <div class="col-md-2">
            Quantity
        </div>
         <div class="col-md-2">
            U.Price($)
        </div>
         <div class="col-md-2">
            Total
        </div>
    </div>
    @for ($i=0; $i <= 4; $i++)
        <div class="row">
            <div class="col-md-2">
                <input type="text" name="properties[{{ $i }}][key]" class="form-control" 
                  value="{{ $product->properties[$i]['key'] ?? '' }}">
            </div>
            <div class="col-md-4">
                <input type="text" name="properties[{{ $i }}][value]" class="form-control" 
                  value="{{ $product->properties[$i]['value'] ?? '' }}">
            </div>
        </div>
    @endfor
</div>