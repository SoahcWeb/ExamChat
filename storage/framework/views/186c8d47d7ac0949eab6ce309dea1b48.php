<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SimpleAsk Test</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        textarea { width: 100%; height: 100px; margin-bottom: 10px; }
        button { padding: 10px 20px; background-color: #3490dc; color: white; border: none; cursor: pointer; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 5px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <h1>SimpleAskService Test</h1>

    <form method="POST" action="<?php echo e(route('ask.test')); ?>">
        <?php echo csrf_field(); ?>
        <label for="question">Pose ta question :</label>
        <textarea id="question" name="question" required><?php echo e(old('question')); ?></textarea>
        <button type="submit">Envoyer</button>
    </form>

    <?php if(isset($response)): ?>
        <h2>Réponse :</h2>
        <pre><?php echo e(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)); ?></pre>
    <?php endif; ?>
</body>
</html>
<?php /**PATH C:\laragon\www\ExamChat\resources\views/prompts/system.blade.php ENDPATH**/ ?>