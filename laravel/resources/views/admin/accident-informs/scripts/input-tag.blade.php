@push('scripts')
    <script>
        var tags = [];

        var $container = document.querySelector('#js-tag-car');
        var $input = document.querySelector('input');
        var $tags = document.querySelector('.js-tags');

        function updateInput(email) {
            $input.value = email;
        }

        $(document).ready(function() {
            $('input[name="is_wounded"], input[name="is_deceased"]').on("click", function() {
                if ($('input[name="is_wounded"]:checked').val() === '{{ STATUS_ACTIVE }}' ||
                    $('input[name="is_deceased"]:checked').val() === '{{ STATUS_ACTIVE }}') {
                    $('#email-input').show();
                    tags = ['Wuthiphat_Ach@truecorp.co.th', 'Suchart_Yut@truecorp.co.th',
                        'Adirek_Int@truecorp.co.th'
                    ];
                    render(tags, $tags);
                    updateInput(tags[0]);
                } else {
                    $('#email-input').hide();
                    tags = [];
                    render(tags, $tags);
                    updateInput('');
                }
            });
        });

        if (tags.length > 0) {
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

            // $container.querySelector('.js-tag-input').focus();
        }
    </script>
@endpush
