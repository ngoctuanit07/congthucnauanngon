# optimzed image
# Version: 1.1
#!/bin/bash
find . -type f -name "*.png" -printf '%p\n' -exec pngcrush -ow -q -reduce -brute "{}" \;
find . -type f \( -name "*.jpg" -o -name "*.jpeg" \) -exec jpegoptim --strip-all --all-progressive "{}" \;