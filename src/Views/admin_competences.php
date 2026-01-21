<?php require_once 'layouts/header.php'; ?>

<div class="dashboard-container">
    <div class="content-wrapper" style="padding: 20px; max-width: 1200px; margin: 0 auto;">
        
        <h1 class="mb-4">Configuration des Compétences</h1>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px;">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 30px;">
            
            <section class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>Ajouter un élément</h3>
                <form action="/admin/competences" method="POST">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Nom de la compétence</label>
                        <input type="text" name="nom" class="form-control" required style="width: 100%; padding: 8px;">
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Catégorie</label>
                        <select name="type_competence" class="form-control" style="width: 100%; padding: 8px;">
                            <option value="technique">Hard Skill (Technique)</option>
                            <option value="comportementale">Soft Skill (Comportementale)</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="4" style="width: 100%; padding: 8px;"></textarea>
                    </div>

                    <button type="submit" name="btn_save" class="btn btn-primary" style="width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; cursor: pointer;">
                        Enregistrer en Base de Données
                    </button>
                </form>
            </section>

            <section class="card" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h3>Compétences Actuelles</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="border-bottom: 2px solid #eee; text-align: left;">
                            <th style="padding: 10px;">Nom</th>
                            <th style="padding: 10px;">Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($competences as $c): ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 10px;"><?php echo htmlspecialchars($c['nom']); ?></td>
                            <td style="padding: 10px;"><span class="badge"><?php echo htmlspecialchars($c['type_competence']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>
</div>

<?php require_once 'layouts/footer.php'; ?>
