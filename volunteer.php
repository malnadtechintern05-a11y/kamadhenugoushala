<?php
/**
 * Volunteer Page — Kamadhenu Goushala
 */
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/csrf.php';

$errors = [];
$old    = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_validate();

    $old = [
        'name'         => sanitize($_POST['name']         ?? ''),
        'email'        => sanitize($_POST['email']        ?? ''),
        'phone'        => sanitize($_POST['phone']        ?? ''),
        'age'          => (int)($_POST['age']             ?? 0),
        'occupation'   => sanitize($_POST['occupation']   ?? ''),
        'availability' => sanitize($_POST['availability'] ?? 'Flexible'),
        'skills'       => sanitize($_POST['skills']       ?? ''),
        'motivation'   => sanitize($_POST['motivation']   ?? ''),
    ];

    $validAvail = ['Weekdays','Weekends','Full Time','Part Time','Flexible'];

    if (empty($old['name']))  $errors[] = 'Your name is required.';
    if (empty($old['email']) || !is_valid_email($old['email'])) $errors[] = 'A valid email is required.';
    if (empty($old['phone'])) $errors[] = 'Phone number is required.';
    if (!in_array($old['availability'], $validAvail, true)) $errors[] = 'Invalid availability option.';
    if (empty($old['motivation'])) $errors[] = 'Please tell us why you want to volunteer.';

    if (empty($errors)) {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO volunteers (name, email, phone, age, occupation, availability, skills, motivation, status)
            VALUES (:name, :email, :phone, :age, :occupation, :availability, :skills, :motivation, 'Pending')
        ");
        $stmt->execute([
            ':name'         => $old['name'],
            ':email'        => $old['email'],
            ':phone'        => $old['phone'],
            ':age'          => $old['age'] ?: null,
            ':occupation'   => $old['occupation'],
            ':availability' => $old['availability'],
            ':skills'       => $old['skills'],
            ':motivation'   => $old['motivation'],
        ]);
        redirect(BASE_URL . '/thank-you.php?type=volunteer');
    }
}

$pageTitle  = 'Volunteer';
$activePage = 'volunteer';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<section class="kg-page-banner">
  <div class="container">
    <h1><i class="bi bi-people me-2"></i>Volunteer With Us</h1>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/">Home</a></li>
        <li class="breadcrumb-item active">Volunteer</li>
      </ol>
    </nav>
  </div>
</section>

<section class="kg-section">
  <div class="container">
    <div class="row g-5">
      <!-- Info -->
      <div class="col-lg-5">
        <div class="kg-section-label">Join the Seva</div>
        <h2 class="kg-section-title">Make a Direct Impact</h2>
        <div class="kg-divider mb-4"></div>
        <p style="color:var(--kg-text-muted);">
          Volunteering at Kamadhenu Goushala is a deeply fulfilling experience. 
          You'll work directly with our cows, participate in daily seva, and become part of 
          a community devoted to Gau Mata.
        </p>

        <h5 class="text-kg-green mt-4 mb-3">What volunteers do:</h5>
        <ul class="list-unstyled" style="color:var(--kg-text-muted);">
          <?php
          $tasks = [
            'Morning & evening cow feeding',
            'Cleaning sheds and water troughs',
            'Assisting the veterinarian',
            'Making A2 ghee and cow products',
            'Conducting Gau Puja and rituals',
            'Administrative and outreach work',
            'Photography and social media',
            'Teaching children about Gau Seva',
          ];
          foreach ($tasks as $task): ?>
          <li class="mb-2 d-flex align-items-start gap-2">
            <i class="bi bi-check-circle-fill mt-1" style="color:var(--kg-green);font-size:.9rem;flex-shrink:0;"></i>
            <span><?= e($task) ?></span>
          </li>
          <?php endforeach; ?>
        </ul>

        <div class="kg-info-box mt-4">
          <p class="mb-0" style="color:var(--kg-green-dark);font-size:.9rem;">
            <strong>Accommodation:</strong> Available for outstation volunteers (subject to availability)<br>
            <strong>Meals:</strong> Satvik prasad meals provided during seva hours<br>
            <strong>Certificate:</strong> Seva certificate issued after 30 days of service
          </p>
        </div>
      </div>

      <!-- Form -->
      <div class="col-lg-7">
        <div class="kg-form-card">
          <h4 class="mb-4 text-kg-green"><i class="bi bi-people-fill me-2"></i>Volunteer Application</h4>

          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger">
            <ul class="mb-0">
              <?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?>
            </ul>
          </div>
          <?php endif; ?>

          <form method="POST" action="" data-validate novalidate>
            <?= csrf_field() ?>

            <div class="row g-3">
              <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Your Full Name <span class="text-danger">*</span></label>
                <input type="text" id="name" name="name" class="form-control"
                       value="<?= e($old['name'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= e($old['email'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                <input type="tel" id="phone" name="phone" class="form-control"
                       value="<?= e($old['phone'] ?? '') ?>" required>
              </div>
              <div class="col-md-6 mb-3">
                <label for="age" class="form-label">Age</label>
                <input type="number" id="age" name="age" class="form-control"
                       min="16" max="99" placeholder="e.g. 28"
                       value="<?= (int)($old['age'] ?? 0) ?: '' ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label for="occupation" class="form-label">Occupation</label>
                <input type="text" id="occupation" name="occupation" class="form-control"
                       placeholder="e.g. Student, Teacher, Farmer…"
                       value="<?= e($old['occupation'] ?? '') ?>">
              </div>
              <div class="col-md-6 mb-3">
                <label for="availability" class="form-label">Availability <span class="text-danger">*</span></label>
                <select name="availability" id="availability" class="form-select" required>
                  <?php foreach (['Weekdays','Weekends','Full Time','Part Time','Flexible'] as $av): ?>
                  <option value="<?= e($av) ?>" <?= ($old['availability'] ?? 'Flexible') === $av ? 'selected':'' ?>><?= e($av) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="mb-3">
              <label for="skills" class="form-label">Skills / Experience</label>
              <textarea name="skills" id="skills" class="form-control" rows="2"
                        placeholder="e.g. veterinary knowledge, carpentry, teaching, photography…"><?= e($old['skills'] ?? '') ?></textarea>
            </div>

            <div class="mb-4">
              <label for="motivation" class="form-label">Why do you want to volunteer? <span class="text-danger">*</span></label>
              <textarea name="motivation" id="motivation" class="form-control" rows="4"
                        placeholder="Tell us about your connection to Gau Seva and why you'd like to join us…"
                        required><?= e($old['motivation'] ?? '') ?></textarea>
              <div class="invalid-feedback">This field is required.</div>
            </div>

            <button type="submit" class="btn btn-kg-primary w-100 py-3" id="volunteerSubmitBtn">
              <i class="bi bi-people-fill me-2"></i>Submit Application
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
