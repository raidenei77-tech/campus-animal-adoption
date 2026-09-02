
<?php
require 'auth.php';
require_login();
require 'db.php';
require 'layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $species = trim($_POST['species'] ?? '');
    $location = trim($_POST['location_found'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $gender = trim($_POST['gender'] ?? 'Unknown');
    $status = $_POST['status'] ?? 'reported';
    $pattern = trim($_POST['pattern'] ?? '');
    $body = trim($_POST['body_colour'] ?? '');
    $eye = trim($_POST['eye_colour'] ?? '');
    $photo = null;

    if (
        isset($_FILES['photo']) &&
        $_FILES['photo']['error'] === UPLOAD_ERR_OK
    ) {
        $ext = strtolower(
            pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION)
        );

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $photo = uniqid('animal_', true) . '.' . $ext;

            move_uploaded_file(
                $_FILES['photo']['tmp_name'],
                __DIR__ . '/uploads/' . $photo
            );
        }
    }

    $uid = $_SESSION['user_id'];

    $stmt = $conn->prepare(
        'INSERT INTO animal
        (
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
            eye_colour,
            photo
        )
        VALUES
        (
            ?, ?, ?, CURDATE(), ?, ?, ?, ?, ?, ?, ?, ?
        )'
    );

    $stmt->bind_param(
        'ssisssssiss',
        $species,
        $location,
        $age,
        $gender,
        $name,
        $status,
        $pattern,
        $body,
        $uid,
        $eye,
        $photo
    );

    $stmt->execute();

    $aid = $conn->insert_id;

    $h = $conn->prepare(
        'INSERT INTO animal_status_history
        (animal_id, status, changed_by)
        VALUES (?, ?, ?)'
    );

    $h->bind_param(
        'isi',
        $aid,
        $status,
        $uid
    );

    $h->execute();

    header(
        'Location: animals.php?msg=' .
        urlencode('Animal registered successfully.')
    );

    exit;
}

$animals = $conn->query(
    'SELECT
        a.*,
        u.full_name
     FROM animal a
     JOIN users u
        ON a.user_id = u.user_id
     ORDER BY a.animal_id DESC'
);

page_top('Animals');

?>

<div class="section-head">

    <h1 class="page-title">
        Animals
    </h1>

    <a
        class="btn secondary"
        href="#add"
    >
        + Register Animal
    </a>

</div>


<div class="grid">

<?php while ($a = $animals->fetch_assoc()): ?>

    <article class="card animal-card">

        <img
            src="<?= h(
                $a['photo']
                    ? 'uploads/' . $a['photo']
                    : 'assets/cat-logo.png'
            ) ?>"
        >

        <h3>

            <?= h($a['name']) ?>

            <span class="tag">
                <?= h($a['species']) ?>
            </span>

        </h3>

        <p>

            <b>Status:</b>
            <?= h(
                str_replace(
                    '_',
                    ' ',
                    $a['status']
                )
            ) ?>

            <br>

            <b>Gender:</b>
            <?= h($a['gender']) ?>

            ·

            <b>Age:</b>
            <?= h($a['age'] ?: 'Unknown') ?>

            <br>

            <b>Found:</b>
            <?= h($a['location_found']) ?>

        </p>

        <p class="muted">
            Registered by <?= h($a['full_name']) ?>
        </p>

        <div class="actions">

            <a
                class="btn small secondary"
                href="animal.php?id=<?= $a['animal_id'] ?>"
            >
                View details
            </a>

            <a
                class="btn small"
                href="adoptions.php?animal_id=<?= $a['animal_id'] ?>"
            >
                Adopt
            </a>

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
    enctype="multipart/form-data"
>

    <input
        name="name"
        placeholder="Animal name"
        required
    >

    <input
        name="species"
        placeholder="Species (Cat/Dog/etc.)"
        required
    >

    <input
        type="number"
        min="0"
        name="age"
        placeholder="Age"
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

        <option value="adopted">
            Adopted
        </option>

    </select>

    <input
        name="pattern"
        placeholder="Pattern"
    >

    <input
        name="body_colour"
        placeholder="Body colour"
    >

    <input
        name="eye_colour"
        placeholder="Eye colour"
    >

    <label class="full">

        Photo

        <input
            type="file"
            name="photo"
            accept="image/*"
        >

    </label>

    <button
        class="btn full"
        type="submit"
    >
        Save Animal
    </button>

</form>


<?php page_bottom(); ?>

