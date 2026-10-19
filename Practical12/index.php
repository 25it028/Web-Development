
<?php
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=event_management;charset=utf8mb4",
    "root",
    "",
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$uploadDir = __DIR__ . "/uploads/";
$maxSize = 2 * 1024 * 1024; // 2 MB
$allowed = [
    "image/jpeg" => "jpg",
    "image/png"  => "png",
    "image/webp" => "webp"
];

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

$message = "";
$editEvent = null;

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

function uploadPoster($file, $dir, $maxSize, $allowed) {
    if ($file["error"] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file["error"] !== UPLOAD_ERR_OK) {
        throw new RuntimeException("Upload failed.");
    }

    if ($file["size"] > $maxSize) {
        throw new RuntimeException("Poster must be 2 MB or smaller.");
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))
        ->file($file["tmp_name"]);

    if (!isset($allowed[$mime]) ||
        getimagesize($file["tmp_name"]) === false) {
        throw new RuntimeException("Only JPG, PNG and WebP images are allowed.");
    }

    $name = bin2hex(random_bytes(16)) . "." . $allowed[$mime];

    if (!move_uploaded_file($file["tmp_name"], $dir . $name)) {
        throw new RuntimeException("Could not save poster.");
    }

    return $name;
}

function removePoster($dir, $name) {
    if ($name && is_file($dir . $name)) {
        unlink($dir . $name);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        if (!hash_equals($_SESSION["csrf"],
            $_POST["csrf"] ?? "")) {
            throw new RuntimeException("Invalid request.");
        }

        $action = $_POST["action"] ?? "";

        if ($action === "delete") {
            $id = filter_var($_POST["id"] ?? "",
                FILTER_VALIDATE_INT);
            if (!$id) throw new RuntimeException("Invalid event ID.");

            $stmt = $pdo->prepare("SELECT poster FROM events WHERE id=?");
            $stmt->execute([$id]);
            $old = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$old) throw new RuntimeException("Event not found.");

            $pdo->prepare("DELETE FROM events WHERE id=?")->execute([$id]);
            removePoster($uploadDir, $old["poster"]);
            $message = "Event deleted successfully.";
        } else {
            $id = filter_var($_POST["id"] ?? "",
                FILTER_VALIDATE_INT);
            $title = trim($_POST["title"] ?? "");
            $description = trim($_POST["description"] ?? "");
            $date = $_POST["event_date"] ?? "";
            $category = trim($_POST["category"] ?? "");
            $status = $_POST["status"] ?? "Open";

            $d = DateTime::createFromFormat("!Y-m-d", $date);
            if (!$title || strlen($title) > 150 ||
                !$d || $d->format("Y-m-d") !== $date ||
                !in_array($status, ["Open", "Closed"], true)) {
                throw new RuntimeException("Enter valid event details.");
            }

            if (strlen($category) > 100) {
                throw new RuntimeException("Category is too long.");
            }

            if ($action === "add") {
                $poster = uploadPoster($_FILES["poster"],
                    $uploadDir, $maxSize, $allowed);

                $stmt = $pdo->prepare(
                    "INSERT INTO events
                    (title,description,event_date,category,status,poster)
                    VALUES (?,?,?,?,?,?)"
                );
                $stmt->execute([
                    $title, $description, $date,
                    $category, $status, $poster
                ]);
                $message = "Event added successfully.";

            } elseif ($action === "update" && $id) {
                $stmt = $pdo->prepare("SELECT poster FROM events WHERE id=?");
                $stmt->execute([$id]);
                $old = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$old) throw new RuntimeException("Event not found.");

                $newPoster = uploadPoster($_FILES["poster"],
                    $uploadDir, $maxSize, $allowed);
                $poster = $newPoster ?? $old["poster"];

                $stmt = $pdo->prepare(
                    "UPDATE events SET title=?, description=?,
                    event_date=?, category=?, status=?, poster=?
                    WHERE id=?"
                );
                $stmt->execute([
                    $title, $description, $date,
                    $category, $status, $poster, $id
                ]);

                if ($newPoster) removePoster($uploadDir, $old["poster"]);
                $message = "Event updated successfully.";
            } else {
                throw new RuntimeException("Invalid action.");
            }
        }
    } catch (Throwable $e) {
        $message = "Error: " . $e->getMessage();
    }
}

if (isset($_GET["edit"])) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id=?");
    $stmt->execute([(int)$_GET["edit"]]);
    $editEvent = $stmt->fetch(PDO::FETCH_ASSOC);
}

$filter = $_GET["status"] ?? "";
if (in_array($filter, ["Open", "Closed"], true)) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE status=? ORDER BY id DESC");
    $stmt->execute([$filter]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $events = $pdo->query("SELECT * FROM events ORDER BY id DESC")
        ->fetchAll(PDO::FETCH_ASSOC);
}

$editing = (bool)$editEvent;
$e = $editEvent ?: [
    "id" => "", "title" => "", "description" => "",
    "event_date" => "", "category" => "", "status" => "Open",
    "poster" => null
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Event Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
 rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-4">
<h1 class="mb-4">Event Management Admin</h1>

<?php if ($message): ?>
<div class="alert <?= str_starts_with($message, "Error:") ? "alert-danger" : "alert-success" ?>">
<?= h($message) ?>
</div>
<?php endif; ?>

<div class="card mb-4">
<div class="card-header"><?= $editing ? "Update Event" : "Add Event" ?></div>
<div class="card-body">
<form method="post" enctype="multipart/form-data">
<input type="hidden" name="csrf" value="<?= h($_SESSION["csrf"]) ?>">
<input type="hidden" name="action" value="<?= $editing ? "update" : "add" ?>">
<input type="hidden" name="id" value="<?= h($e["id"]) ?>">
<input type="hidden" name="MAX_FILE_SIZE" value="2097152">

<div class="mb-3">
<label class="form-label">Event Title</label>
<input class="form-control" name="title" maxlength="150"
 required value="<?= h($e["title"]) ?>">
</div>

<div class="mb-3">
<label class="form-label">Description</label>
<textarea class="form-control" name="description"><?= h($e["description"]) ?></textarea>
</div>

<div class="mb-3">
<label class="form-label">Event Date</label>
<input type="date" class="form-control" name="event_date"
 required value="<?= h($e["event_date"]) ?>">
</div>

<div class="mb-3">
<label class="form-label">Category</label>
<input class="form-control" name="category" maxlength="100"
 value="<?= h($e["category"]) ?>">
</div>

<div class="mb-3">
<label class="form-label">Status</label>
<select class="form-select" name="status">
<?php foreach (["Open","Closed"] as $s): ?>
<option value="<?= $s ?>" <?= $e["status"] === $s ? "selected" : "" ?>><?= $s ?></option>
<?php endforeach; ?>
</select>
</div>

<div class="mb-3">
<label class="form-label">Poster (JPG, PNG, WebP; max 2 MB)</label>
<input type="file" class="form-control" name="poster"
 accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp">
<?php if ($e["poster"]): ?>
<p class="mt-2">Current poster: <?= h($e["poster"]) ?></p>
<img src="uploads/<?= rawurlencode($e["poster"]) ?>"
 alt="Current poster" style="max-width:150px">
<?php endif; ?>
</div>

<button class="btn btn-primary"><?= $editing ? "Update Event" : "Add Event" ?></button>
<?php if ($editing): ?>
<a class="btn btn-secondary" href="index.php">Cancel</a>
<?php endif; ?>
</form>
</div>
</div>

<form method="get" class="mb-3 d-flex gap-2">
<select class="form-select" name="status" style="max-width:200px">
<option value="">All statuses</option>
<option value="Open" <?= $filter === "Open" ? "selected" : "" ?>>Open</option>
<option value="Closed" <?= $filter === "Closed" ? "selected" : "" ?>>Closed</option>
</select>
<button class="btn btn-outline-primary">Filter</button>
<a href="index.php" class="btn btn-outline-secondary">Reset</a>
</form>

<div class="card">
<div class="card-header">All Events</div>
<div class="table-responsive">
<table class="table table-striped table-hover mb-0">
<thead><tr><th>ID</th><th>Poster</th><th>Title</th>
<th>Date</th><th>Category</th><th>Status</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($events as $event): ?>
<tr>
<td><?= h($event["id"]) ?></td>
<td><?php if ($event["poster"]): ?>
<img src="uploads/<?= rawurlencode($event["poster"]) ?>"
 alt="Poster" style="width:65px;height:55px;object-fit:cover">
<?php else: ?>No poster<?php endif; ?></td>
<td><?= h($event["title"]) ?></td>
<td><?= h($event["event_date"]) ?></td>
<td><?= h($event["category"]) ?></td>
<td><?= h($event["status"]) ?></td>
<td>
<a class="btn btn-sm btn-warning mb-1"
 href="?edit=<?= h($event["id"]) ?>">Edit</a>
<form method="post" class="d-inline"
 onsubmit="return confirm('Delete this event?')">
<input type="hidden" name="csrf" value="<?= h($_SESSION["csrf"]) ?>">
<input type="hidden" name="action" value="delete">
<input type="hidden" name="id" value="<?= h($event["id"]) ?>">
<button class="btn btn-sm btn-danger mb-1">Delete</button>
</form>
</td>
</tr>
<?php endforeach; ?>
<?php if (!$events): ?>
<tr><td colspan="7" class="text-center">No events found.</td></tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>
</body>
</html>
