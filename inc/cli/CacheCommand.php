<?php

namespace Required\Traduttore\CLI;

use GP;
use GP_Translation_Set;
use WP_CLI;
use WP_CLI_Command;

/**
 * Manages the Traduttore cache.
 *
 * @since 2.0.0
 */
class CacheCommand extends WP_CLI_Command {
	/**
	 * Removes the cached Git repository for a given project.
	 *
	 * Finds the project the repository belongs to and removes the checked out Git repository completely.
	 *
	 * Useful when the local repository was somehow corrupted.
	 *
	 * ## OPTIONS
	 *
	 * <project|url>
	 * : Project path / ID or GitHub repository URL, e.g. https://github.com/wearerequired/required-valencia
	 *
	 * ## EXAMPLES
	 *
	 *     # Update translations from repository URL.
	 *     $ wp traduttore cache clear https://github.com/wearerequired/required-valencia
	 *     Success: Removed cached Git repository for project (ID: 123)!
	 *
	 *     # Update translations from project path.
	 *     $ wp traduttore cache clear required/required-valencia
	 *     Success: Removed cached Git repository for project (ID: 123)!
	 *
	 *     # Update translations from project ID.
	 *     $ wp traduttore cache clear 123
	 *     Success: Removed cached Git repository for project (ID: 123)!
	 */
	public function clear( $args, $assoc_args ) {
		$locator = new ProjectLocator( $args[0] );
		$project = $locator->get_project();

		if ( ! $project ) {
			WP_CLI::error( 'Project not found' );
		}

		$github_updater = new GitHubUpdater( $project );

		$success = $github_updater->remove_local_repository();

		if ( $success ) {
			WP_CLI::success( sprintf( 'Removed cached Git repository for project (ID: %d)!', $project->id ) );
		} else {
			WP_CLI::error( sprintf( 'Could not remove cached Git repository for project (ID: %d)!', $project->id ) );
		}
	}
}