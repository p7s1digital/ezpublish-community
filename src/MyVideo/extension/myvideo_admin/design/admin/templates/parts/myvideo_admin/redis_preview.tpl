{* @see http://stackoverflow.com/questions/4810841/json-pretty-print-using-javascript
*}
{literal}
    <style type="text/css">
        textarea.redis-preview,
        #redis-preview pre {
            width: 90%;
            outline: 1px solid #ccc;
            padding: 5px;
            margin: 5px;
        }

        #redis-preview .string {
            color: green;
        }

        #redis-preview .number {
            color: darkorange;
        }

        #redis-preview .boolean {
            color: blue;
        }

        #redis-preview .null {
            color: magenta;
        }

        #redis-preview .key {
            color: red;
        }
    </style>
    <script type="text/javascript">
        function output(inp) {
            var target = document.getElementById('redis-preview');
            var newChild = document.createElement('pre');
            newChild.innerHTML = inp;
            target.appendChild(newChild);
        }

        function syntaxHighlight(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = 'number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }
    </script>
{/literal}


<div id="redis-preview">
</div>

<textarea rows="5" class="redis-preview">{$vars.data}</textarea>

{literal}
<script type="text/javascript">
    var ez = ez || {};
    ez.givenPreviewJson = {/literal}{$vars|json}{literal};
    if (ez.givenPreviewJson.data !== "NULL") {
        var obj = {/literal}{$vars.data}{literal};
        var str = JSON.stringify(obj, undefined, 4);
        output(syntaxHighlight(str));
    }
</script>
{/literal}

<hr/>