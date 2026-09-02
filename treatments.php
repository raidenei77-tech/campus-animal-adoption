
<?php

require 'auth.php';
require_login();
require 'db.php';
require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SESSION['role'] === 'volunteer') {

    /* =========================
       ADD NEW VETERINARIAN
       ========================= */

    if (isset($_POST['add_vet'])) {

        $name = trim($_POST['vet_name']);
        $avail = trim($_POST['availability_status']);
        $spec = trim($_POST['specialization']);
        $qual = trim($_POST['qualification']);
        $yrs = (int)$_POST['experienced_years'];
        $email = trim($_POST['email']);
        $lic = trim($_POST['license_no']);
        $phone = trim($_POST['phone_number']);

        $s = $conn->prepare(
            'INSERT INTO vets
            (
                vet_name,
                availability_status,
                specialization,
                qualification,
                experienced_years,
                email,
                license_no,
                phone_number
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $s->bind_param(
            'ssssisss',
            $name,
            $avail,
            $spec,
            $qual,
            $yrs,
            $email,
            $lic,
            $phone
        );

        $s->execute();

        header(
            'Location: treatments.php?msg=' .
            urlencode('Vet added.')
        );

        exit;
    }


    /* =========================
       RECORD NEW TREATMENT
       ========================= */

    $vet = (int)$_POST['vet_id'];
    $animal = (int)$_POST['animal_id'];

    $time = str_replace(
        'T',
        ' ',
        $_POST['treatment_time']
    );

    $type = $_POST['treatment_type'];
    $status = $_POST['status'];

    $medicine_name = trim(
        $_POST['medicine_name'] ?? ''
    );

    $desc = trim(
        $_POST['description'] ?? ''
    );


    /* INSERT TREATMENT */

    $s = $conn->prepare(
        'INSERT INTO treatment
        (
            vet_id,
            animal_id,
            treatment_time,
            treatment_type,
            status,
            medicine_name
        )
        VALUES (?, ?, ?, ?, ?, ?)'
    );

    $s->bind_param(
        'iissss',
        $vet,
        $animal,
        $time,
        $type,
        $status,
        $medicine_name
    );

    $s->execute();

    $tid = $conn->insert_id;


    /* INSERT DESCRIPTION */

    $d = $conn->prepare(
        'INSERT INTO treatment_description
        (
            treatment_id,
            description
        )
        VALUES (?, ?)'
    );

    $d->bind_param(
        'is',
        $tid,
        $desc
    );

    $d->execute();


    /* UPDATE ANIMAL STATUS */

    $conn->query(
        "UPDATE animal
         SET status='under_treatment'
         WHERE animal_id=" . $animal . "
         AND status<>'adopted'"
    );


    header(
        'Location: treatments.php?msg=' .
        urlencode('Treatment recorded.')
    );

    exit;
}


/* =========================
   GET ANIMALS
   ========================= */

$animals = $conn->query(
    'SELECT
        animal_id,
        name
     FROM animal
     ORDER BY name'
);


/* =========================
   GET VETERINARIANS
   ========================= */

$vets = $conn->query(
    'SELECT *
     FROM vets
     ORDER BY vet_name'
);


/* =========================
   SECOND VET QUERY
   FOR DISPLAY
   ========================= */

$vets2 = $conn->query(
    'SELECT *
     FROM vets
     ORDER BY vet_name'
);


/* =========================
   GET TREATMENT HISTORY
   ========================= */

$rows = $conn->query(
    'SELECT
        t.*,
        a.name AS animal_name,
        v.vet_name,
        d.description
     FROM treatment t
     JOIN animal a
        ON t.animal_id = a.animal_id
     JOIN vets v
        ON t.vet_id = v.vet_id
     LEFT JOIN treatment_description d
        ON t.treatment_id = d.treatment_id
     ORDER BY t.treatment_time DESC'
);

page_top('Treatments');

?>


<div class="section-head">

    <h1 class="page-title">
        Veterinary & Treatment
    </h1>

</div>


<?php if ($_SESSION['role'] === 'volunteer'): ?>

<div class="grid">


    <!-- =========================
         RECORD TREATMENT
         ========================= -->

    <form
        class="form-card form-grid"
        method="post"
    >

        <h2 class="full">
            Record treatment
        </h2>


        <!-- ANIMAL -->

        <select
            name="animal_id"
            required
        >

            <option value="">
                Animal
            </option>

            <?php while ($a = $animals->fetch_assoc()): ?>

                <option value="<?= $a['animal_id'] ?>">

                    <?= h($a['name']) ?>

                </option>

            <?php endwhile; ?>

        </select>


        <!-- VET -->

        <select
            name="vet_id"
            required
        >

            <option value="">
                Vet
            </option>

            <?php while ($v = $vets->fetch_assoc()): ?>

                <option value="<?= $v['vet_id'] ?>">

                    <?= h($v['vet_name']) ?>

                </option>

            <?php endwhile; ?>

        </select>


        <!-- TREATMENT TIME -->

        <input
            type="datetime-local"
            name="treatment_time"
            required
        >


        <!-- TREATMENT TYPE -->

        <select
            name="treatment_type"
            required
        >

            <option value="vaccination">
                Vaccination
            </option>

            <option value="surgery">
                Surgery
            </option>

            <option value="medication">
                Medication
            </option>

            <option value="others">
                Others
            </option>

        </select>


        <!-- STATUS -->

        <select
            name="status"
            required
        >

            <option value="ongoing">
                Ongoing
            </option>

            <option value="completed">
                Completed
            </option>

            <option value="cancelled">
                Cancelled
            </option>

        </select>


        <!-- MEDICINE NAME -->

        <input
            name="medicine_name"
            placeholder="Medicine name"
        >


        <!-- DESCRIPTION -->

        <textarea
            class="full"
            name="description"
            placeholder="Treatment description"
            required
        ></textarea>


        <!-- SAVE -->

        <button
            class="btn full"
            type="submit"
        >
            Save Treatment
        </button>

    </form>



    <!-- =========================
         ADD VETERINARIAN
         ========================= -->

    <form
        class="form-card form-grid"
        method="post"
    >

        <h2 class="full">
            Add veterinarian
        </h2>


        <input
            type="hidden"
            name="add_vet"
            value="1"
        >


        <input
            name="vet_name"
            placeholder="Vet name"
            required
        >


        <input
            name="specialization"
            placeholder="Specialization"
            required
        >


        <input
            name="qualification"
            placeholder="Qualification"
            required
        >


        <input
            type="number"
            name="experienced_years"
            min="0"
            placeholder="Years experience"
        >


        <input
            type="email"
            name="email"
            placeholder="Email"
            required
        >


        <input
            name="phone_number"
            placeholder="Phone"
        >


        <input
            name="license_no"
            placeholder="License number"
            required
        >


        <select
            name="availability_status"
        >

            <option value="Available">
                Available
            </option>

            <option value="Busy">
                Busy
            </option>

            <option value="Unavailable">
                Unavailable
            </option>

        </select>


        <button
            class="btn full"
            type="submit"
        >
            Add Vet
        </button>

    </form>

</div>

<?php endif; ?>



<!-- =========================
     VETERINARIANS
     ========================= -->

<div class="section-head">

    <h2>
        Veterinarians
    </h2>

</div>


<div class="grid">

    <?php while ($v = $vets2->fetch_assoc()): ?>

        <div class="card">

            <h3>
                Dr. <?= h($v['vet_name']) ?>
            </h3>


            <p>

                <?= h($v['specialization']) ?>

                <br>

                <?= h($v['qualification']) ?>

                <br>

                <?= h($v['experienced_years']) ?>

                years experience

            </p>


            <span class="tag">

                <?= h($v['availability_status']) ?>

            </span>


            <p class="muted">

                <?= h($v['email']) ?>

                ·

                <?= h($v['phone_number']) ?>

            </p>

        </div>

    <?php endwhile; ?>

</div>



<!-- =========================
     TREATMENT HISTORY
     ========================= -->

<div class="section-head">

    <h2>
        Treatment history
    </h2>

</div>


<div class="table-wrap">

    <table>

        <tr>

            <th>
                Animal
            </th>

            <th>
                Vet
            </th>

            <th>
                Date
            </th>

            <th>
                Type
            </th>

            <th>
                Status
            </th>

            <th>
                Description
            </th>

        </tr>


        <?php while ($r = $rows->fetch_assoc()): ?>

            <tr>

                <td>
                    <?= h($r['animal_name']) ?>
                </td>


                <td>
                    <?= h($r['vet_name']) ?>
                </td>


                <td>
                    <?= h($r['treatment_time']) ?>
                </td>


                <td>
                    <?= h($r['treatment_type']) ?>
                </td>


                <td>

                    <span class="tag">

                        <?= h($r['status']) ?>

                    </span>

                </td>


                <td>
                    <?= h($r['description'] ?? '') ?>
                </td>

            </tr>

        <?php endwhile; ?>

    </table>

</div>


<?php

page_bottom();

?>

