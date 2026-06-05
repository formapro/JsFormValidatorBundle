{
  description = "Development shell for FpJsFormValidatorBundle";

  inputs = {
    nixpkgs.url = "github:NixOS/nixpkgs/nixos-25.11";
  };

  outputs = { nixpkgs, ... }:
    let
      systems = [
        "aarch64-darwin"
        "aarch64-linux"
        "x86_64-darwin"
        "x86_64-linux"
      ];

      forAllSystems = nixpkgs.lib.genAttrs systems;
    in
    {
      devShells = forAllSystems (system:
        let
          pkgs = import nixpkgs { inherit system; };

          php = pkgs.php85.buildEnv {
            extensions = { enabled, all }:
              enabled ++ [
                all.intl
                all.xdebug
              ];
            extraConfig = ''
              xdebug.mode=coverage
            '';
          };

          composer = pkgs.writeShellScriptBin "composer" ''
            exec ${php}/bin/php ${pkgs.php85Packages.composer}/bin/.composer-wrapped "$@"
          '';

          cypressLinuxLibs = with pkgs; [
            alsa-lib
            gtk3
            libgbm
            nss
            xorg.libX11
            xorg.libXScrnSaver
            xorg.libXcomposite
            xorg.libXdamage
            xorg.libXext
            xorg.libXfixes
            xorg.libXrandr
            xorg.libXtst
          ];
        in
        {
          default = pkgs.mkShell {
            packages = with pkgs; [
              composer
              git
              nodejs_22
              php
              unzip
              zip
            ] ++ lib.optionals stdenv.isLinux [
              xorg.xauth
              xorg.xorgserver
            ];

            LD_LIBRARY_PATH = pkgs.lib.optionalString pkgs.stdenv.isLinux (
              pkgs.lib.makeLibraryPath cypressLinuxLibs
            );

            shellHook = ''
              export COMPOSER_HOME="$PWD/.cache/composer"
              export NPM_CONFIG_CACHE="$PWD/.cache/npm"
              export CYPRESS_CACHE_FOLDER="$PWD/.cache/Cypress"
              export PATH="$PWD/vendor/bin:$PWD/node_modules/.bin:$PWD/Tests/app/node_modules/.bin:$PATH"

              mkdir -p "$COMPOSER_HOME" "$NPM_CONFIG_CACHE" "$CYPRESS_CACHE_FOLDER"

              echo "JsFormValidatorBundle dev shell: PHP $(php -r 'echo PHP_VERSION;'), Node $(node --version), Composer ${pkgs.php85Packages.composer.version}"
            '';
          };
        });

      formatter = forAllSystems (system:
        let
          pkgs = import nixpkgs { inherit system; };
        in
        pkgs.nixpkgs-fmt);
    };
}
