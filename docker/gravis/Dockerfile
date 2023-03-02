FROM nginx/unit:1.26.1-python3.9

RUN apt-get update
RUN apt-get install software-properties-common -y
RUN apt update
RUN apt update
RUN apt install graphviz graphviz-dev -y

COPY requirements.txt /tmp/requirements.txt
RUN pip install --no-cache-dir -r /tmp/requirements.txt

RUN pip install selenium
RUN pip install pygraphviz
RUN pip install gravis

#COPY ./start.sh /start.sh
#RUN chmod +x /start.sh

#COPY ./gunicorn_conf.py /gunicorn_conf.py

#COPY ./start-reload.sh /start-reload.sh
#RUN chmod +x /start-reload.sh

#COPY ./app /app
#WORKDIR /app/

#ENV PYTHONPATH=/app

COPY files /workspace/files
RUN chown -R unit:unit /workspace/files

COPY config.json /docker-entrypoint.d/config.json

ENV PYTHONPATH=./app

#RUN chown -R unit:unit app
#RUN chown -R unit:unit ./files

EXPOSE 80

# Run the start script, it will check for an /app/prestart.sh script (e.g. for migrations)
# And then will start Gunicorn with Uvicorn
#CMD ["/start.sh"]