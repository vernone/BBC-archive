<?php

  $conn = new PDO("mysql:host=localhost;dbname=umwelt", "sullivanver", "DVlwy4zJDB6bECfZ", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

  $statement = $conn->query('SELECT ID, lannee FROM annee');
  $annees = $statement->fetchAll(PDO::FETCH_ASSOC);

  $statement = $conn->query('SELECT ID, type_objet FROM objet');
  $objets = $statement->fetchAll(PDO::FETCH_ASSOC);

  $statement = $conn->query('SELECT * FROM action');
  $actions = $statement->fetchAll(PDO::FETCH_ASSOC);

  $statement = $conn->query('SELECT ID, nom_lieu FROM lieu');
  $lieux = $statement->fetchAll(PDO::FETCH_ASSOC);

  $statement = $conn->query('SELECT ID, name FROM sample');
  $names = $statement->fetchAll(PDO::FETCH_ASSOC);

  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);


  $requete = "SELECT sample.name, sample.reference, sample.duree FROM sample ";
  $requete2 = "SELECT sample.reference, sample.name FROM sample ";

  if(isset($_POST['annee']) && $_POST['annee'] != 'all'){
    $filter_annee = "JOIN sample_annee ON sample.ID = sample_annee.ID_sample
                      JOIN annee ON sample_annee.ID_annee = annee.ID WHERE annee.ID = '".$_POST['annee']."'";

    $requete = $requete.$filter_annee;
    $requete2 = $requete.$filter_annee;
    //echo $requete;
  }else if(isset($_POST['objet']) && $_POST['objet'] != 'all'){
    $filter_objet = "JOIN sample_objet ON sample.ID = sample_objet.ID_sample
                      JOIN objet ON sample_objet.ID_objet = objet.ID WHERE objet.ID = '".$_POST['objet']."'";

    $requete = $requete.$filter_objet;
    $requete2 = $requete.$filter_objet;
  }else if(isset($_POST['action']) && $_POST['action'] != 'all'){

    $filter_action = "JOIN sample_action ON sample.ID = sample_action.ID_sample
                      JOIN action ON sample_action.ID_action = action.ID WHERE action.ID = '".$_POST['action']."'";
    $requete = $requete.$filter_action;
    $requete2 = $requete.$filter_action;

  }else if(isset($_POST['lieu']) && $_POST['lieu'] != 'all'){

    $filter_lieu = "JOIN sample_lieu ON sample.ID = sample_lieu.ID_sample
                    JOIN lieu ON sample_lieu.ID_lieu = lieu.ID WHERE lieu.ID = '".$_POST['lieu']."'";

    $requete = $requete.$filter_lieu;
    $requete2 = $requete.$filter_lieu;
  }

  $statement = $conn->query($requete);
  $samples = $statement->fetchAll(PDO::FETCH_ASSOC);

  $statement = $conn->query($requete2);
  $nomsamples = $statement->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>BBC sans</title>
    <link rel="stylesheet" type="text/css" href="css/reset.css" />
    <link rel="stylesheet" type="text/css" href="css/main.css" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script src="build/three.js"></script>
  	<script type="text/javascript" src="js/controls/FirstPersonControls.js"></script>
  	<script type="text/javascript" src="js/WebGL.js"></script>
  	<script type="text/javascript" src="js/libs/dat.gui.min.js"></script>


  </head>
  <body>
    
    <div id="overlay">
  		<div>
  			<button id="startButton">Pièce n°1</button>
  		</div>
  	</div>
    <script>

        if (WEBGL.isWebGLAvailable() === false) {
          document.body.appendChild(WEBGL.getWebGLErrorMessage());
        }

        var camera, controls, scene, renderer, light;
        var material1, material2, material3;
        var analyser1, analyser2, analyser3;
        var clock = new THREE.Clock();
        var startButton = document.getElementById('startButton');
        startButton.addEventListener('click', init);

        var sphere = new THREE.SphereBufferGeometry(20, 32, 16);

        function init() {
  			var overlay = document.getElementById('overlay');
  			overlay.remove();
 
        camera = new THREE.PerspectiveCamera(50, window.innerWidth / window.innerHeight, 1, 10000);
  			camera.position.set(0, 25, 0);

  			var listener = new THREE.AudioListener();
  			camera.add(listener);

 			 	scene = new THREE.Scene();
 				light = new THREE.DirectionalLight(0xffffff);
 				scene.add( new THREE.AmbientLight( Math.random() * 0xff0000 ) );
 				light.position.set(0, 0.5, 1).normalize();
 				scene.add(light);
        var startAngle = 0;
        var radius = 70;
        var diameter = radius*4;
        var centerX=-5;
        var centerZ= 0.5;
        var mathpi = Math.PI/180;
        var startRadians = startAngle + mathpi;
        var arraysamples = <?php echo json_encode($samples);?>;
        var arraynomsamples = <?php echo json_encode($nomsamples);?>;
        var rangee = Math.round((Object.keys(arraysamples).length) / 5);
        var incrementAngle = 360/totalson;
        var incrementRadians = incrementAngle * mathpi;
        var totalson = 5;
        var yp = 30;
        var audioLoader = new THREE.AudioLoader();

          for ( var n = 0; n < rangee; n ++) {
            for ( var i = 0; i < totalson; i ++ ) {

                var object = new THREE.Mesh( sphere, new THREE.MeshLambertMaterial( { color: Math.random() * 0xffffff } ) );

                var xp = centerX + Math.sin(startRadians) * radius;
                var zp = centerZ + Math.cos(startRadians) * radius;
                object.position.z = xp;
                object.position.x = zp;
                object.position.y = yp;
                var a = n * 5 + i;
                var path = "sounds/" + arraynomsamples[a].reference + arraynomsamples[a].name + ".wav";
                var sound = new THREE.PositionalAudio(listener);

                audioLoader.load(path, function (buffer) {
                sound.setBuffer(buffer);
                sound.setRefDistance(20);
                sound.play();
                sound.setLoop(true);
                sound.setVolume(1);
                });
                scene.add(object);
                object.add(sound);
                startRadians += incrementRadians;
            }

            // totalson = totalson + 3;
            radius = radius + 100;
            startAngle = startAngle + 150;


            var startRadians = startAngle + mathpi;
            var incrementAngle = 360/totalson;
            var incrementRadians = incrementAngle * mathpi;

            if (yp > 59) {
                yp = yp - 30;
            } else {
                yp = yp + 30;
            }
          }

          var helper = new THREE.GridHelper(1000, 10, 0x444444, 0x444444);
          helper.position.y = 0.1;
          scene.add(helper);

          renderer = new THREE.WebGLRenderer({ antialias: true });
          renderer.setPixelRatio(window.devicePixelRatio);
          renderer.setSize(window.innerWidth, window.innerHeight);
          document.body.appendChild(renderer.domElement);

          /////// camera controls
          controls = new THREE.FirstPersonControls(camera, renderer.domElement);

          controls.movementSpeed = 200;
          controls.lookSpeed = 0.15;
          // controls.noFly = true;
          // controls.lookVertical = false;

          window.addEventListener('resize', onWindowResize, false);

          animate();

          }
          ///// fin init

          ///// resize
          function onWindowResize() {

          camera.aspect = window.innerWidth / window.innerHeight;
          camera.updateProjectionMatrix();

          renderer.setSize(window.innerWidth, window.innerHeight);

          controls.handleResize();

          }

          ///// arrow key
          document.addEventListener('keydown',onDocumentKeyDown,false);
          function onDocumentKeyDown(event){
              var delta = 200;
              event = event || window.event;
              var keycode = event.keyCode;
              switch(keycode){
              case 37 :
              camera.position.x = camera.position.x - delta;
              break;
              case 38 :
              camera.position.z = camera.position.z - delta;
              break;
              case 39 :
              camera.position.x = camera.position.x + delta;
              break;
              case 40 :
              camera.position.z = camera.position.z + delta;
              break;
            }
          document.addEventListener('keyup',onDocumentKeyUp,false);
           }
          function onDocumentKeyUp(event){
          document.removeEventListener('keydown',onDocumentKeyDown,false);
            }

          function animate() {
          requestAnimationFrame(animate);
          render();
            }

          function render() {
          camera.updateProjectionMatrix();
          var delta = clock.getDelta();

          controls.update(delta);

          renderer.render(scene, camera);
          }

          </script>
  </body>
</html>
