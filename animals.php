<?php

require 'auth.php';
require_login();
require 'db.php';
require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();

    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $location = trim($_POST['location_found'] ?? '');

    $ageRaw = trim($_POST['age'] ?? '');
    $age = $ageRaw === '' ? null : max(0, (int)$ageRaw);

    $gender = $_POST['gender'] ?? 'Unknown';
    $status = $_POST['status'] ?? 'reported';

    $pattern = trim($_POST['pattern'] ?? '');
    $body = trim($_POST['body_colour'] ?? '');
    $eye = trim($_POST['eye_colour'] ?? '');

    $uid = (int)$_SESSION['user_id'];

    $allowedStatuses = [
        'reported',
        'rescued',
        'under_treatment',
        'available',
        'adopted'
    ];

    $allowedGenders = [
        'Female',
        'Male',
        'Unknown'
    ];

    if (
        $name === '' ||
        $species === '' ||
        $location === '' ||
        !in_array($status, $allowedStatuses, true) ||
        !in_array($gender, $allowedGenders, true)
    ) {
        redirect_with_message(
            'animals.php',
            'Please complete the required animal information.'
        );
    }

    $conn->begin_transaction();

    try {

        $stmt = $conn->prepare(
            'INSERT INTO animal(
                species,
                location_found,
                age,
                date_registered,
                gender,
                name,
                status,
                pattern,
                body_colour,
                user_id,
                eye_colour
            )
            VALUES(
                ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?
            )'
        );

        $stmt->bind_param(
            'ssisssssis',
            $species,
            $location,
            $age,
            $gender,
            $name,
            $status,
            $pattern,
            $body,
            $uid,
            $eye
        );

        $stmt->execute();

        $aid = $conn->insert_id;

        $hist = $conn->prepare(
            'INSERT INTO animal_status_history(
                animal_id,
                status,
                changed_by
            )
            VALUES(?,?,?)'
        );

        $hist->bind_param(
            'isi',
            $aid,
            $status,
            $uid
        );

        $hist->execute();

        $conn->commit();

        redirect_with_message(
            'animals.php',
            'Animal registered successfully.'
        );

    } catch (Throwable $e) {

        $conn->rollback();
        throw $e;
    }
}


/* ---------------- SEARCH AND FILTER ---------------- */

$q = trim($_GET['q'] ?? '');
$statusFilter = $_GET['status'] ?? '';

$sql = '
    SELECT a.*, u.full_name
    FROM animal a
    JOIN users u
        ON a.user_id = u.user_id
    WHERE 1=1
';

$params = [];
$types = '';

if ($q !== '') {

    $sql .= '
        AND (
            a.name LIKE ?
            OR a.species LIKE ?
            OR a.location_found LIKE ?
        )
    ';

    $like = "%$q%";

    $params = [
        $like,
        $like,
        $like
    ];

    $types .= 'sss';
}

if (
    $statusFilter !== '' &&
    in_array(
        $statusFilter,
        [
            'reported',
            'rescued',
            'under_treatment',
            'available',
            'adopted'
        ],
        true
    )
) {

    $sql .= ' AND a.status=?';

    $params[] = $statusFilter;
    $types .= 's';
}

$sql .= ' ORDER BY a.animal_id DESC';

$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();

$animals = $stmt->get_result();

page_top('Animals');

?>


<div class="section-head">

    <h1 class="page-title">
        Animals
    </h1>

    <a class="btn secondary" href="#add">
        + Register Animal
    </a>

</div>


<form class="filter-bar" method="get">

    <input
        name="q"
        value="<?=h($q)?>"
        placeholder="Search name, species or location"
    >

    <select name="status">

        <option value="">
            All statuses
        </option>

        <?php
        foreach (
            [
                'reported' => 'Reported',
                'rescued' => 'Rescued',
                'under_treatment' => 'Under treatment',
                'available' => 'Available',
                'adopted' => 'Adopted'
            ]
            as $v => $label
        ):
        ?>

            <option
                value="<?=$v?>"
                <?=$statusFilter === $v ? 'selected' : ''?>
            >
                <?=$label?>
            </option>

        <?php endforeach; ?>

    </select>

    <button class="btn small">
        Search
    </button>

    <a
        class="btn small secondary"
        href="animals.php"
    >
        Reset
    </a>

</form>


<div class="grid">

<?php if ($animals->num_rows === 0): ?>

    <div class="empty card">
        No animals found.
    </div>

<?php endif; ?>


<?php while ($a = $animals->fetch_assoc()): ?>

<article class="card animal-card">

    <h3>

        <?=h($a['name'])?>

        <span class="tag">
            <?=h($a['species'])?>
        </span>

    </h3>

    <p>

        <b>Status:</b>
        <?=h(str_replace('_', ' ', $a['status']))?>

        <br>

        <b>Gender:</b>
        <?=h($a['gender'])?>

        ·

        <b>Age:</b>
        <?=h(
            $a['age'] === null
            ? 'Unknown'
            : $a['age']
        )?>

        <br>

        <b>Found:</b>
        <?=h($a['location_found'])?>

    </p>

    <p class="muted">

        Registered by
        <?=h($a['full_name'])?>

    </p>


    <div class="actions">

        <a
            class="btn small secondary"
            href="animal.php?id=<?=$a['animal_id']?>"
        >
            View details
        </a>


        <?php if ($a['status'] === 'available'): ?>

            <a
                class="btn small"
                href="adoptions.php?animal_id=<?=$a['animal_id']?>"
            >
                Adopt
            </a>

        <?php endif; ?>

    </div>

</article>

<?php endwhile; ?>

</div>



<div
    class="section-head"
    id="add"
>

    <h2>
        Register a new animal
    </h2>

</div>


<form
    class="form-card form-grid"
    method="post"
>

    <?=csrf_field()?>


    <input
        name="name"
        placeholder="Animal name"
        required
        maxlength="200"
    >


    <input
        name="species"
        placeholder="Species (Cat/Dog/etc.)"
        required
        maxlength="100"
    >


    <input
        type="number"
        min="0"
        name="age"
        placeholder="Age (optional)"
    >


    <select name="gender">

        <option>Female</option>
        <option>Male</option>
        <option>Unknown</option>

    </select>


    <input
        class="full"
        name="location_found"
        placeholder="Location found"
        required
        maxlength="500"
    >


    <select name="status">

        <option value="reported">
            Reported
        </option>

        <option value="rescued">
            Rescued
        </option>

        <option value="under_treatment">
            Under treatment
        </option>

        <option value="available">
            Available for adoption
        </option>

        <?php if (is_volunteer()): ?>

            <option value="adopted">
                Adopted
            </option>

        <?php endif; ?>

    </select>


    <input
        name="pattern"
        placeholder="Pattern"
        maxlength="200"
    >


    <input
        name="body_colour"
        placeholder="Body colour"
        maxlength="100"
    >


    <input
        name="eye_colour"
        placeholder="Eye colour"
        maxlength="100"
    >


    <button class="btn full">
        Save Animal
    </button>

</form>


<?php page_bottom(); ?>

