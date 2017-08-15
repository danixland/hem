<?php
    $pagetitle = "Your Accounts";

    get_header($pagetitle);
?>
		<section>
            <nav>
                <ul>
					<li>
						<a href="admin/login.php">login</a>
					</li>
					<li>
						<a href="admin/login.php?action=newuser">new user</a>
					</li>
					<?php if ( $hemUsers->logged_in ) : ?>
					<ul>
						<li>
							<a href="transactions.php">transactions</a>
						</li>
						<li>
							<a href="accounts.php">accounts</a>
						</li>
					</ul>
					<?php endif; ?>
                </ul>
            </nav>

			<?php $install_file = "admin/install.php";
			if ( file_exists($install_file) ) : ?>
			<article>
				<header>
					<h2>CAREFUL, THE FILE INSTALL.PHP IS STILL AVAILABLE!!</h2>
				</header><!-- /header -->
				<p>in a production environment it is advised to remove or rename the file install.php in order to avoid wiping the database by mistake.</p>
			</article>
			<?php endif; ?>
		</section>

<?php get_footer(); ?>
