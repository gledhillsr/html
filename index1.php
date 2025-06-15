<?php
$patrols = [
    ["name" => "Afton Alps", "href" => "/Afton", "img" => "images/AftonLogo.jpg"],
    ["name" => "Andes Tower Hills", "href" => "/Andes", "img" => "images/andes_logo.jpg"],
    ["name" => "Antelope Butte", "href" => "/AntelopeButte", "img" => "images/AntelopeButte.png"],
    ["name" => "BigRock", "href" => "/BigRock", "img" => "images/BigRock.jpeg"],
    ["name" => "Black River Basin", "href" => "/BlackjackMountain", "img" => "images/SnowRiver.png"],
    ["name" => "Black Mountain", "href" => "/BlackMountain", "img" => "images/BlackMountain2.png"],
    ["name" => "Brighton", "href" => "/Brighton", "img" => "images/Brighton.gif"],
    ["name" => "Buena Vista", "href" => "/BuenaVista", "img" => "images/BuenaVista.gif"],
    ["name" => "Casper Mountain", "href" => "/CasperMountain", "img" => "images/CasperMountain.png"],
    ["name" => "Coffee Mill", "href" => "/CoffeeMill", "img" => "images/CoffeeMillLogo.png"],
    ["name" => "Detroit Mountain", "href" => "/DetroitMountain", "img" => "images/DetroitMountain.png"],
    ["name" => "Devil's Head", "href" => "/DevilsHead", "img" => "images/DevilsHeadSkiPatrol.png"],
    ["name" => "Grand Targhee Hosts", "href" => "/GrandTargheeHosts", "img" => "images/GrandTarghee.jpg"],
    ["name" => "Great Divide", "href" => "/GreatDivide", "img" => "images/GreatDivide.jpg"],
    ["name" => "Hermon Mountain", "href" => "/HermonMountain", "img" => "images/HermonMountain.jpg"],
    ["name" => "Hesperus", "href" => "/Hesperus", "img" => "images/Hesperus.jpg"],
    ["name" => "Holiday Mountain", "href" => "/HolidayMountain", "img" => "images/SkiHolidayMtn.jpg"],
    ["name" => "Hyland Hills", "href" => "/HylandHills", "img" => "images/ThreeRivers.jpg"],
    ["name" => "Idaho Falls Nordic", "href" => "/IFNordic", "img" => "images/IFNordic.gif"],
    ["name" => "Jackson Creek Summit", "href" => "/IndianHeadMountain", "img" => "images/SnowRiver.png"],
    ["name" => "Kelly Canyon", "href" => "/KellyCanyon", "img" => "images/KellyCanyon.jpg"],
    ["name" => "Lee Canyon", "href" => "/LeeCanyon", "img" => "images/LeeCanyon.png"],
    ["name" => "Lonesome Pine Trails", "href" => "/LonesomePine", "img" => "images/lonesomepines.gif"],
    ["name" => "Magic Mountain", "href" => "/MagicMountain", "img" => "images/MagicMountain.jpg"],
    ["name" => "Magic Mountain Snowsports School", "href" => "/psiaMagicMountain", "img" => "images/MagicMountain.jpg"],
    ["name" => "Meadowlark", "href" => "/Meadowlark", "img" => "images/Meadowlark.png"],
    ["name" => "Mount Kato", "href" => "/MountKato", "img" => "images/MountKato.jpg"],
    ["name" => "Mount Pleasant", "href" => "/MountPleasant", "img" => "images/MountPleasant.png"],
    ["name" => "Norway Mountain", "href" => "/NorwayMountain", "img" => "images/NorwayMtnLogo.png"],
    ["name" => "Norway Mountain Ski Instructors", "href" => "/psiaPineMountain", "img" => "images/NorwayMtnLogo.png"],
    ["name" => "Paul Bunyan", "href" => "/PaulBunyan", "img" => "images/PaulBunyan.jpg"],
    ["name" => "Pine Creek", "href" => "/PineCreek", "img" => "images/pinecreek.gif"],
    ["name" => "Pine Mountain", "href" => "/PineMountain", "img" => "images/PineMtnLogo.png"],
    ["name" => "Pine Mountain Ski Instructors", "href" => "/psiaPineMountain", "img" => "images/psiaPineMountain.png"],
    ["name" => "Plattekill Mountain", "href" => "/Plattekill", "img" => "images/PlattekillLogo.png"],
    ["name" => "Pomerelle", "href" => "/Pomerelle", "img" => "images/PomerellePatrol.jpg"],
    ["name" => "Ragged Mountain", "href" => "/RMSP", "img" => "images/RMSP_logo.JPG"],
    ["name" => "Snowbowl", "href" => "/Snowbowl", "img" => "images/SnowBowlLogo.jpg"],
    ["name" => "SnowCreek", "href" => "/SnowCreek", "img" => "images/SnowCreek.jpg"],
    ["name" => "SnowCreek Paid", "href" => "/PaidSnowCreek", "img" => "images/SnowCreek.jpg"],
    ["name" => "Snow King", "href" => "/SnowKing", "img" => "images/SnowKing.jpg"],
    ["name" => "Soldier Mountain", "href" => "/SoldierMountain", "img" => "images/SoldierMountain.gif"],
    ["name" => "Steeplechase", "href" => "/Steeplechase", "img" => "images/Steeplechase.png"],
    ["name" => "Teton Valley Mountain Patrol", "href" => "/GrandTarghee", "img" => "images/GrandTarghee.jpg"],
    ["name" => "White Pine", "href" => "/WhitePine", "img" => "images/WhitePine.jpg"],
    ["name" => "Willamette Backcountry", "href" => "/Willamette", "img" => "images/Willamette.jpeg"],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="Content-Language" content="en-us">
  <meta name="google-site-verification" content="9tOSs__EmJBJhIfC4vcxrC-Qodre5JB7NM8cqm1o-0Y">
  <title>Ski Patrol Online Calendar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .alert-custom {
      background-color: #fff3cd;
      color: #856404;
    }
    .station-card {
      padding: 0.5rem 1rem;
    }
    .station-card img {
      max-height: 50px;
      width: 30%;
      object-fit: contain;
      flex-shrink: 0;
    }
    .station-card h5 {
      font-size: 1rem;
      margin-bottom: 0;
    }
    .card-body {
      flex-grow: 1;
    }
  </style>
</head>
<body>
<div class="container py-4">
  <div class="alert alert-danger">
    <strong>This site is NOT affiliated with the National Ski Patrol.</strong><br>
    Just a single ski patroller volunteering his time, efforts, and money to assist other ski patrols.
  </div>
  <div class="alert alert-custom">
    <strong>Important Notice:</strong><br>
    If you're accessing this site using <code>nsponline.org</code>, please use <code>gledhills.com</code> instead.<br>
    The old URL will be surrendered due to trademark issues. Gledhills.com will continue to support this service.<br>
    Thank you for your understanding — <strong>Steven Gledhill</strong>.
  </div>

  <h2 class="mt-4">Ski Patrol Scheduling Links</h2>
  <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 mt-2">
      <?php foreach ($patrols as $patrol): ?>
        <div class="col">
          <a href="<?= htmlspecialchars($patrol['href']) ?>" class="text-decoration-none text-dark">
            <div class="card station-card d-flex flex-row align-items-center">
              <img src="<?= htmlspecialchars($patrol['img']) ?>" alt="<?= htmlspecialchars($patrol['name']) ?>">
              <div class="card-body">
                <h5 class="card-title mb-0"><?= htmlspecialchars($patrol['name']) ?></h5>
              </div>
            </div>
          </a>
        </div>
      <?php endforeach; ?>
  </div>

  <footer class="mt-4 text-center">
    <p class="text-muted mb-0">
      This is a free service to ski patrols that are members of the National Ski Patrol.<br>
      <a href="mailto:Steve@Gledhills.com?sub=Questions about online scheduling">Contact Webmaster</a> — Steve Gledhill (Brighton Ski Resort Patroller)
    </p>
  </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
