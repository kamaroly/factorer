<tr class="edit-item" data-id='{{{ $item->id !!}'>
    <td> {!! $item->id !!} </td>
    <td> {!! $item->name !!} </td>
    <td> {!! $item->price !!} </td>
    <td> {!! $item->quantity !!} </td>
    <td> {!! $item->description !!} </td>
    <td> 
    	<a href="{!! route('items.edit',['id'=>$item->id]) !!}" class="btn btn-primary"><i class="fa fa-edit"></i></a>
    	<a href="{!! route('items.delete',['id'=>$item->id]) !!}" class="btn btn-danger"><i class="fa fa-remove"></i></a>

    </td>
</tr>