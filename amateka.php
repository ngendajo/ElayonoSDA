<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Elayono</title>
    <link rel="icon" href="images/sdalogo.png" type="image/x-icon">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="header.css" rel="stylesheet">
    <link href="footer.css" rel="stylesheet">
    <style>
         .history-section {
            background-color: #f8f9fa;
        }
        .history-image {
            max-height: 200px;
            object-fit: cover;
            margin-bottom: 1rem;
        }
        .history-text {
            font-size: 1.1rem;
            line-height: 1.8;
        }
        .title-section{
            font-weight:bold;
            font-size:3rem;
            color: rgb(56, 48, 112);
        }
        .login-button{
            padding: 10px;
            background-color: #007BFF;
            font-size: 14px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer; 
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <div class="main-wrapper">
            <?php
            include('header.php');
            ?>

            <main class="container-fluid mt-3">
                <section class="history-section py-5">
                    <div class="container">
                        <h2 class="text-center mb-5 title-section">Amateka y' Itorero Rya Elayono Intara ya Mujyejuru</h2>
                        <div class="row align-items-start">
                            <div class="col-md-4 mb-4 mb-md-0">
                                <img src="images/inshutinziza.jpg" alt="Church History 1" class="img-fluid rounded shadow-lg history-image w-100">
                                <img src="images/elayonchurch.PNG" alt="Church History 2" class="img-fluid rounded shadow-lg history-image w-100">
                            </div>
                            <div class="col-md-8">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-4">
                                        <h3 class="card-title mb-4">Urugendo rwo Kwizera</h3>
                                        <div class="history-text">
                                            <p>Amateka y’Itorero rya Elayono mu Ntara y’Ivugabutumwa ya Mujyejuru atangira mu mwaka wa 2016, aho habaye ivuna ryakozwe na Rushenyi Patrick mu mudugudu wa Nyamagana, akagari ka Nyamagana, umurenge wa Ruhango, akarere ka Ruhango, Intara y’Amajyepfo y’u Rwanda. Iri vuna ryateguwe n’Itorero rya Mujyejuru, rikaba ryari rigamije guhindura ubuzima bw’abantu benshi, cyane cyane mu bijyanye no gufasha abatuye muri ako gace kwinjira mu Itorero.</p>
                                            <p>Ryabaye mu rwego rwo guteza imbere imibereho myiza no kugera ku ntego z’ivugabutumwa. Abantu batandukanye bitabiriye iri vuna, kandi niho hagiye hashyirwamo gahunda nyinshi zo gutangiza ibikorwa by’ivugabutumwa mu buryo bwagutse. Iri vuna ryari rifite intego yo kuzamura abizera, bityo hakaba hari igitekerezo cyo gukomeza no gushyigikira ibikorwa by’Itorero rya Mujyejuru.</p>
                                            <p>Mu mwaka wa 2018, tariki ya 3 Nzeri, hatangijwe Itorero rya Elayono, ryashyizweho nk’ishami rya Mujyejuru. Iri torero ryaje kwitwa Itorero ry’Abadiventiste b’Umunsi wa Karindwi rya Elayono. Kwitwa Elayono byari bigamije kwerekana ubuzima bushya n’imbaraga z’ivugabutumwa zishaka gusakara mu gihugu.</p>
                                            <p>Icyo gihe, Itorero ryatangiye gusengera mu gisharagati, ahantu hatari hateguwe. Ariko uko iminsi yagiye ishira, abizera batangiye gushaka uburyo bwo guteza imbere ibikorwa byabo, cyane cyane mu kubaka ahantu ho gusengerera. Babashije kubona inkunga zitandukanye zaturutse mu matorero yo mu bindi bice, ndetse n’abayoboke bashyigikiye iyubakwa ry’urusengero.</p>
                                            <p>Uko imyaka yagendaga ishira, abizera bagiye bakusanya ubushobozi n'inkunga zitandukanye, kugira ngo bashyireho urusengero rwabo. Inkunga zaturukaga mu baturanyi b’Itorero rya Mujyejuru, ndetse n’andi matorero yo mu Ntara zagiye zifasha mu kugera ku ntego yo kubaka urusengero ruhanitse.</p>
                                            <p>Kubaka urusengero rwari ikintu gikomeye, kuko byafashije abizera kubona ahantu ho gukorera imirimo y’ivugabutumwa, ndetse no kugira ahantu ho gusengerera. Uru rusengero rwari rufite igisobanuro gikomeye mu gutanga ubwisungane mu by’Imana no mu kwigisha ubutumwa bwiza ku buzima bwa buri munsi.</p>
                                            <p>Muri rusange, Itorero rya Elayono ryahereye ku nkingi z’imibereho myiza y'abantu, bityo riba igikorwa cy'ivugabutumwa cyagiriye akamaro abakristo benshi bo mu Ntara y’Amajyepfo. Ubu ni umuryango w’abizera uhamye, ufite intego yo gukomeza gukura no kwagura ibikorwa by’ivugabutumwa mu gihugu cyose.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

            </main>
            <?php
            include('footer.php');
            ?>
        </div>
        <?php
        include('aside.php');
        ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>