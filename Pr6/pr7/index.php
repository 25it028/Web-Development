```php
<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>

    <style>
        body {
            font-family: Arial;
            background-color: #f2f2f2;
        }

        .container {
            width: 400px;
            margin: 50px auto;
            background: white;
            padding: 25px;
            border-radius: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #333;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>

<div class="container">

    <h2>Student Registration</h2>

    <form action="process.php" method="POST">

        <label>Name:</label>
        <input type="text" name="name">

        <label>Email:</label>
        <input type="text" name="email">

        <label>Mobile:</label>
        <input type="text" name="mobile">

        <label>Course:</label>
        <select name="course">
            <option value="">Select Course</option>
            <option value="B.Tech IT">B.Tech IT</option>
            <option value="B.Tech CSE">B.Tech CSE</option>
            <option value="BCA">BCA</option>
            <option value="MCA">MCA</option>
        </select>

        <input type="submit" value="Register">

    </form>

</div>

</body>
</html>
```
