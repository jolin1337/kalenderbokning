FROM jolin1337/base-environment:v1.0

USER root
#RUN curl -o- https://raw.githubusercontent.com/creationix/nvm/v0.35.1/install.sh | bash
RUN npm cache clean -f && npm install -g n
RUN n stable
RUN npm install -g @vue/cli
RUN npm install -g @vue/cli-init
USER dev