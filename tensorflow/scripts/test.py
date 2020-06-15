# import tensorflow as tf
#
# print(tf.__version__)

# import tensorflow as tf
# msg = tf.constant('Hello, TensorFlow!')
# tf.print(msg)



import tensorflow as tf

hello = tf.constant('Hello, TensorFlow')

sess = tf.Session()

print(sess.run(hello))