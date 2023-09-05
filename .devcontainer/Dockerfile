#-------------------------------------------------------------------------------------------------------------
# Copyright (c) Microsoft Corporation. All rights reserved.
# Licensed under the MIT License. See https://go.microsoft.com/fwlink/?linkid=2090316 for license information.
#-------------------------------------------------------------------------------------------------------------

FROM php:7-cli

# Avoid warnings by switching to noninteractive
ENV DEBIAN_FRONTEND=noninteractive

# This Dockerfile adds a non-root 'vscode' user with sudo access. However, for Linux,
# this user's GID/UID must match your local user UID/GID to avoid permission issues
# with bind mounts. Update USER_UID / USER_GID if yours is not 1000. See
# https://aka.ms/vscode-remote/containers/non-root-user for details.
ARG USERNAME=vscode
ARG USER_UID=1000
ARG USER_GID=$USER_UID

# Configure apt and install packages
RUN apt-get update
RUN apt-get -y install --no-install-recommends apt-utils dialog 2>&1
RUN apt-get -y install curl git iproute2 procps lsb-release unzip zip openssl gnupg

# Create a non-root user to use if preferred - see https://aka.ms/vscode-remote/containers/non-root-user.
RUN groupadd --gid $USER_GID $USERNAME
RUN useradd -s /bin/bash --uid $USER_UID --gid $USER_GID -m $USERNAME
# [Optional] Add sudo support for the non-root user
RUN apt-get install -y sudo
RUN echo $USERNAME ALL=\(root\) NOPASSWD:ALL > /etc/sudoers.d/$USERNAME
RUN chmod 0440 /etc/sudoers.d/$USERNAME

# Install Composer from: https://hub.docker.com/_/composer
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN echo 'export PATH="$PATH:$HOME/.composer/vendor/bin:/root/.composer/vendor/bin:vendor/bin"' >> /root/.bashrc

#RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -
RUN apt-get install -y ca-certificates curl gnupg
RUN mkdir -p /etc/apt/keyrings
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource-repo.gpg.key | sudo gpg --dearmor -o /etc/apt/keyrings/nodesource.gpg
RUN echo "deb [signed-by=/etc/apt/keyrings/nodesource.gpg] https://deb.nodesource.com/node_16.x nodistro main" | sudo tee /etc/apt/sources.list.d/nodesource.list
RUN apt-get update
RUN apt-get install -y nodejs

#other packages
RUN apt-get install -y libzip-dev \
    && docker-php-ext-configure zip  \
    && docker-php-ext-install zip \
    && docker-php-ext-install bcmath mysqli pdo pdo_mysql
RUN su $USERNAME -c "composer global require laravel/installer"
RUN echo "alias art='php artisan'" >> /root/.bashrc && \
    echo "alias serve='php artisan serve --port=8000 --host=0.0.0.0'" >> /root/.bashrc && \
    echo "alias ls='ls -B -h --color=auto -ltr'" >> /root/.bashrc && \
    echo "alias _='sudo'" >> /root/.bashrc  && \
    echo "alias apt='sudo apt'" >> /root/.bashrc  && \
    echo "alias apt-get='sudo apt-get'" >> /root/.bashrc

# Switch back to dialog for any ad-hoc use of apt-get
ENV DEBIAN_FRONTEND=