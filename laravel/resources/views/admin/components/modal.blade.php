<div class="modal fade" id="{{$id}}-modal" data-target="{{$id}}-modal" aria-labelledby="{{$id}}-modal" aria-hidden="false">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-popout">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="{{$id}}-title" class="modal-title">
                    @if(!empty($icon))
                    <i class="{{$icon}}"></i>
                    @else
                    <i class="icon-document"></i>
                    @endif
                    {{$title}}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                {{$slot}}
            </div>
            @isset($footer)
            <div class="modal-footer">
                {{$footer}}
            </div>
            @endisset
        </div>
    </div>
</div>