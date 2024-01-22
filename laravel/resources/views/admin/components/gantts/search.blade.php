<div class="row push">
    <div class="col-sm-12 d-flex justify-content-start">
        <div>
            <label for="search" class="text-start col-form-label">{{ $search_text ?? '' }}</label>
            <input type="text" class="form-control" v-model="search" @keyup="filterSearch"
                placeholder="{{ __('lang.search_placeholder') }}">
        </div>
    </div>
</div>