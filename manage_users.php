<?php
// Get all users with their profile data
$users = $conn->query("
    SELECT u.*, d.department, des.designation, eval.firstname as eval_firstname, eval.lastname as eval_lastname
    FROM users u
    LEFT JOIN department_list d ON u.department_id = d.id
    LEFT JOIN designation_list des ON u.designation_id = des.id
    LEFT JOIN users eval ON u.evaluator_id = eval.id
    ORDER BY u.role, u.firstname
");
?>

<!-- Display users in a table -->
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Department</th>
            <th>Designation</th>
            <th>Evaluator</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while($user = $users->fetch_assoc()): ?>
        <tr>
            <td><?php echo $user['firstname'] . ' ' . $user['lastname'] ?></td>
            <td><?php echo $user['email'] ?></td>
            <td><?php echo ucfirst($user['role']) ?></td>
            <td><?php echo $user['department'] ?? 'N/A' ?></td>
            <td><?php echo $user['designation'] ?? 'N/A' ?></td>
            <td><?php echo isset($user['eval_firstname']) ? $user['eval_firstname'] . ' ' . $user['eval_lastname'] : 'N/A' ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                <a href="delete_user.php?id=<?php echo $user['id'] ?>" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>