<h1>Willkommen beim Trier Therapie Navigator</h1>

<p>Die Seite sieht im moment noch etwas kahl aus. Drück den unteren Button, um mit der Installation zu beginnen und etwas Farbe hinein zu bringen.</p>

<p>Zur Installation wird <a href="https://yarnpkg.com">Yarn</a> benötigt</p>
<button role="button" onclick="installLibraries();">Installation beginnen</button>

<p id="loading-view">

</p>

<script>
/* Es gibt zu diesem Zeitpunkt noch kein JQuerry oder andere Bibliotheken. Es muss daher alles mit reinem Javascript erfolgen */
    function installLibraries(){
        var xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if (this.readyState == 4) {
                window.location.href = '<?php echo site_url('setup/step2');?>';
            }
        }
        xhttp.open('GET', '<?php echo site_url('setup/install_libs');?>', true);
        xhttp.timeout = 0;
        xhttp.send();
        var elem = document.getElementById('loading-view');
        elem.innerHTML = 'Wird installiert'
        setInterval(() => {           
            elem.innerHTML = elem.innerHTML + '.'
        }, 500);
    }
</script>