<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;

class DuskServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Browser::macro('select2', function ($element = null, $value = null) {
            $random = Str::random(10);
            $this->script(" var opt" . $random . " = new Option('" . $value . "', '" . $value . "', false, false); $('" . $element . "').append(opt" . $random . ").trigger('change'); ");
            return $this;
        });

        Browser::macro('jsClick', function ($element = null) {
            $this->script(" document.querySelector('" . $element . "').click(); ");
            return $this;
        });

        Browser::macro('jsAttach', function ($path, $index) {
            $this->script("
            fetch('$path')
            .then((res) => {
                const filenameHeader = res.headers.get('Content-Disposition');
                const match = filenameHeader && filenameHeader.match(/filename=[''](.+)['']/);
                const contentType = res.headers.get('Content-Type');
                const fileType = contentType ? contentType.split('/')[1] : '';
                const filename = match ? match[1] : 'fileUpload.' + fileType;
                return res.blob().then((blob) => ({
                    blob,
                    filename
                }));
            })
            .then(({
                blob,
                filename
            }) => {
                const file = new File([blob], filename, {
                    type: blob.type
                });

                window.myDropzone[$index].addFile(file);
            });
        ");
        });

        Browser::macro('jsValueAll', function ($element = null, $value = null) {
            $this->script(" $('" . $element . "').val(" . $value . "); ");
            return $this;
        });

        Browser::macro('jsKeyUp', function ($element = null) {
            $this->script(" $('" . $element . "').keyup(); ");
            return $this;
        });

        Browser::macro('jsMouseUp', function ($element = null) {
            $this->script(" $('" . $element . "').mouseup(); ");
            return $this;
        });

        Browser::macro('select2Open', function ($element = null) {
            $this->script(" 
                $('" . $element . "').select2('open');
            ");
            return $this;
        });

        Browser::macro('select2Value', function ($element = null, $value = null) {
            $this->script(" 
                $('" . $element . "').val('". $value ."').trigger('change');
            ");
            return $this;
        });
    }
}
