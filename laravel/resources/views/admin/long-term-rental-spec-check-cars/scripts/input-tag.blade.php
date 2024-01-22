@push('scripts')
    <script>
        var tags = [];
        $(document).ready(function() {

            $(".email").click(function() {
                var tag = $(this).data('email');
                if (tag != '') {
                    tags.push(tag);
                }

            });
        });

        $('#modal-share-dealer').on('hidden.bs.modal', function() {
            tags = [];
        });

        var $container = document.querySelector('#js-tag-car');
        var $input = document.querySelector('input');
        var $tags = document.querySelector('.js-tags');

        if (tags.length > 0 && tags[0] != '') {
            render(tags, $tags);
        }
        $container.addEventListener('click', function() {

            $input.focus();
        });

        $container.addEventListener('keydown', function(evt) {
            if (!evt.target.matches('.js-tag-input')) {
                return;
            }

            if (evt.keyCode !== 13) {
                return;
            }

            var value = String(evt.target.value);
            tags.push(evt.target.value);

            $input.value = '';
            render(tags, $tags);
        });

        $container.addEventListener('keydown', function(evt) {
            if (!evt.target.matches('.js-tag-input')) {
                return;
            }

            if (evt.keyCode !== 44) {
                return;
            }

            console.log('value.length ' + String(evt.target.value).length);
            if (String(evt.target.value).length) {
                return;
            }

            tags = tags.slice(0, tags.length - 1);
            console.log('tags.length-1 ' + tags);
            $input.value = '';
            render(tags, $tags);
        });

        $container.addEventListener('click', function(evt) {
            if (evt.target.matches('.js-tag-close') || evt.target.matches('.js-tag')) {
                tags = tags.filter(function(tag, i) {
                    return i != evt.target.getAttribute('data-index');
                });
                render(tags, $tags);
            }
        }, true);


        function render(tags, el) {
            el.innerHTML = tags.map(function(tag, i) {
                return (
                    '<div class="tag js-tag" id="js-tag-car" data-index="' + i + '">' +
                    tag +
                    '<span class="tag-close js-tag-close" data-index="' + i + '">×</span>' +
                    '</div>'
                );
            }).join('') + ('<input placeholder="ระบุข้อมูล..." class="js-tag-input">');

            $container.querySelector('.js-tag-input').focus();

        }
    </script>
@endpush
